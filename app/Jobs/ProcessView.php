<?php

namespace App\Jobs;

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
    public $timeout = 90;

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
     * Create a new job instance.
     */
    public function __construct(Model $viewable, Collection $visitor, string $collection = null)
    {
        $this->viewable = $viewable;
        $this->visitor = $visitor;
        $this->collection = $collection;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        views($this->viewable)
            ->collection($this->collection)
            ->overrideIpAddress($this->visitor->get('ipAddress', '127.0.0.1'))
            ->overrideVisitor($this->visitor->get('visitorId', 0))
            ->delayInSession(now()->addDays(1))
            ->record();
    }
}
