<?php
namespace App\EventSubscriber;

use App\Services\RedisContainer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class NewsSubscriber implements EventSubscriber
{

    protected $redis;

    public function __construct(RedisContainer $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @return array|string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->sitemapFlagSet();
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->sitemapFlagSet();
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->sitemapFlagSet();
    }


    private function sitemapFlagSet()
    {
        $this->redis->getRedis()->set('system.is_sitemap_generate', 1);
    }
}