<?php

namespace App\Command;

use App\Entity\NewsEntity;
use App\Services\RedisContainer;
use http\Env;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class GenerateSitemapCommand extends Command
{
    const PATH_TO_SITEMAP = '/app/public/sitemap.xml';

    protected static $defaultName = 'GenerateSitemap';

    protected $container;
    protected $router;
    protected $appHost;
    protected $redisService;

    public function __construct($appHost, ContainerInterface $container, RouterInterface $router, RedisContainer $redis)
    {
        parent::__construct();
        $this->container = $container;
        $this->router = $router;
        $this->appHost = $appHost;
        $this->redisService = $redis;
    }

    protected function configure()
    {
        $this->setDescription('Крон комманда для генерации sitemap');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);
        $isStartGenerate = $this->redisService->getRedis()->get('system.is_sitemap_generate');
        if(empty($isStartGenerate)) {
            $io->caution('Генерация sitemap не требуется');
            return 0;
        }

        $eManager = $this->container->get('doctrine')->getManager();

        $context = $this->router->getContext();
        $context->setBaseUrl($this->appHost);

        $simpleXml = new \SimpleXMLElement('<urlset />');
        $simpleXml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $indexPageRoot = $simpleXml->addChild('url');
        $indexPageRoot->addChild('loc', $this->router->generate('news_entity_index'));
        foreach($eManager->getRepository(NewsEntity::class)->findForSitemap() ?? [] as $article) {
            $child = $simpleXml->addChild('url');
            $child->addChild('loc', $this->router->generate('news_entity_show', ['slug' => $article->getSlug()]));
            $child->addChild('lastmod', $article->getUpdatedAt()->format('Y-m-d\TH:i:s+00:00'));
        }
        file_put_contents(self::PATH_TO_SITEMAP, $simpleXml->asXML());

        $io->success('Генерация sitemap завершена');
        $this->redisService->getRedis()->delete('system.is_sitemap_generate');
        return 1;
    }
}
