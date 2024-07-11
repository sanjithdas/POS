<?php

namespace App\Jobs;

use App\Mail\OrderCreated;
use App\Mail\OrderDeleted;
use App\Mail\OrderUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $type;
    protected $user;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order, $type, $user)
    {
        $this->order = $order;
        $this->type = $type;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->type) {
            case 'created':
                try{
                Mail::to($this->user->email)->send(new OrderCreated($this->order, $this->user));
                }catch(Throwable $e){
                    return response()->json([
                        'error' => $e->getMessage()
                    ]);
                }
                break;

            case 'updated':
                Mail::to($this->user->email)->send(new OrderUpdated($this->order, $this->user));
                break;

            case 'deleted':
                Mail::to($this->user->email)->send(new OrderDeleted($this->user));
                break;
        }
    }
}
