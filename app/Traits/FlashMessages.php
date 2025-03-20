<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait FlashMessages
{
    protected function flashSuccess($message)
    {
        session()->flash('success', $message);
    }

    protected function flashError($message)
    {
        session()->flash('error', $message);
    }

    protected function logError($message, \Exception $exception = null)
    {
        Log::error($message);
        if ($exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getTraceAsString());
        }
    }

    protected function handleException(\Exception $e, $redirectRoute = null)
    {
        $this->logError('An error occurred', $e);
        $this->flashError('An error occurred. Please try again.');

        if ($redirectRoute) {
            return redirect()->route($redirectRoute)->withInput();
        }

        return back()->withInput();
    }

    protected function flashWarning($message)
    {
        session()->flash('warning', $message);
    }
}
