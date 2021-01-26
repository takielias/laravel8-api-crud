<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidateUserLoginRequest implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $email;
    private $pass;
    private $msg;

    public function __construct(Request $request)
    {
        $this->email = $request->email;
        $this->pass = $request->password;
        $this->msg = 'Invalid Email/Password.';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (Auth::attempt(['email' => $this->email, 'password' => $this->pass])) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->msg;
    }
}
