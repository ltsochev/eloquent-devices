<?php

namespace Ltsochev\Auth;

use RuntimeException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Auth\EloquentUserProvider as LaravelEloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class EloquentUserProvider extends LaravelEloquentUserProvider
{
    /**
     * Current HTTP request
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Database table for remember tokens
     *
     * @var string
     */
    protected $tokenTable;

    /**
     * Create a new database user provider. It keeps track of multiple
     * remember me tokens so that our users can access the website using
     * multipe devices. The driver for this provider is "eloquentdevices".
     *
     * @param \Illuminate\Database\ConnectionInterface $conn
     * @param \Illuminate\Contracts\Hashing\Hasher     $hasher
     * @param \Illuminate\Http\Request                 $request
     * @param array                                    $config
     */
    public function __construct(ConnectionInterface $conn, HasherContract $hasher, Request $request, array $config)
    {
        if (!array_key_exists('token_table', $config)) {
            throw new RuntimeException("Missing 'token_table' setting in the configuration file.");
        }

        parent::__construct($hasher, $config['model']);

        $this->conn = $conn;
        $this->request = $request;
        $this->tokenTable = $config['token_table'];
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $user = $this->conn->table($this->tokenTable)
                    ->where('user_id', $identifier)
                    ->where('remember_token', $token)
                    ->first();

        if (! is_null($user)) {
            $model = $this->createModel();
            return $model->newQuery()
                    ->where($model->getKeyName(), $identifier)
                    ->first();
        }

        // Backwards compatibility
        return parent::retrieveByToken($identifier, $token);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        $user->setRememberToken($token);

        $user->save();

        $this->conn->table($this->tokenTable)->insert([
            'user_id' => $user->getAuthIdentifier(),
            'remember_token' => $token,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->header('User-Agent'),
            'updated_at' => Carbon::now(),
        ]);
    }
}
