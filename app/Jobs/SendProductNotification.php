<?php

namespace App\Jobs;

use App\Mail\ProductCreated;
use App\Mail\ProductDeleted;
use App\Mail\ProductUpdated;
use Carbon\Exceptions\Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;
use Throwable;

class SendProductNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    protected $type;
    protected $user;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product, $type, $user)
    {
        $this->product = $product;
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
                Mail::to($this->user->email)->send(new ProductCreated($this->product, $this->user));
                }catch(Throwable $e){
                    return response()->json([
                        'error' => $e->getMessage()
                    ]);
                }
                break;

            case 'updated':
                Mail::to($this->user->email)->send(new ProductUpdated($this->product, $this->user));
                break;

            case 'deleted':
                Mail::to($this->user->email)->send(new ProductDeleted($this->user));
                break;
        }
    }
}
