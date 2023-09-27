<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rule_env = Rule::unique('apps')
            ->where('db_type', '=', $this->db_type)
            ->where('db_host', '=', $this->db_host)
            ->where('db_port', '=', $this->db_port);
        $rule_domain = Rule::unique('apps')
            ->where('http_port', $this->http_port);

        return [
            'client_id'         => 'required|uuid|exists:clients,id',
            'application'       => 'required|in:client,agent',
            'domain'            => ['required', 'string', $rule_domain],
            'http_port'         => 'nullable|integer',
            'redis_host'        => 'required|regex:/^[a-z0-9.-]+$/',
            'redis_port'        => 'nullable|integer',
            'redis_pass'        => 'nullable|string',
            'db_type'           => 'required|string|in:mysql,pgsql,sqlite',
            'db_host'           => 'required|string',
            'db_name'           => ['required', 'regex:/^[a-z0-9_]+$/', $rule_env],
            'db_port'           => 'required|numeric',
            'db_user'           => 'required|regex:/^[a-zA-Z0-9_]+$/',
            'db_pass'           => 'nullable|string',
            'cache_driver'      => 'nullable|in:redis,memcached,file',
            'session_driver'    => 'nullable|in:redis,memcached,file',
        ];
    }
}
