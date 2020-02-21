<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ProcessView implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var int
     */
    public $tries = 3;

    /**
     * @var int
     */
    public $timeout = 60;

    /**
     * @var Model
     */
    public $viewable;

    /**
     * @var Collection
     */
    public $visitor;

    /**
     * @var string
     */
    public $collection;

    /**
     * @var Carbon|null
     */
    public $cooldown;

    /**
     * Create a new job instance.
     */
    public function __construct(
        Model $viewable, Collection $visitor, string $collection = null, $cooldown = null
    ) {
        $this->viewable = $viewable;
        $this->visitor = $visitor;
        $this->collection = $collection;
        $this->cooldown = $cooldown;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        views($this->viewable)
            ->collection($this->collection)
            ->useIpAddress($this->visitor->get('ipAddress', '127.0.0.1'))
            ->useVisitor($this->visitor->get('visitorId', 0))
            ->delayInSession($this->cooldown ?? now()->addHour())
            ->record();
    }
}
