<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class ApiResponse
{
    /**
     * Return a successful redirect response with flash message
     */
    public static function redirectSuccess(
        string $route,
        string $message = 'Success',
        mixed $data = null
    ): RedirectResponse {
        return Redirect::route($route)->with('success', $message);
    }

    /**
     * Return an error redirect response with flash errors
     */
    public static function redirectError(
        string $route,
        string $message = 'An error occurred',
        array $errors = []
    ): RedirectResponse {
        $redirect = Redirect::route($route);
        
        if (!empty($errors)) {
            return $redirect->withErrors($errors);
        }
        
        return $redirect->withErrors(['message' => $message]);
    }

    /**
     * Return a back redirect with success message
     */
    public static function backSuccess(string $message = 'Success'): RedirectResponse
    {
        return redirect()->back()->with('success', $message);
    }

    /**
     * Return a back redirect with error message
     */
    public static function backError(string $message = 'An error occurred', array $errors = []): RedirectResponse
    {
        $redirect = redirect()->back();
        
        if (!empty($errors)) {
            return $redirect->withErrors($errors);
        }
        
        return $redirect->withErrors(['message' => $message]);
    }
}
