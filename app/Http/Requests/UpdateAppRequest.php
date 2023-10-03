<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppRequest extends FormRequest
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
        if ($this->use_custom) {
            $rule_env = Rule::unique('apps')
                ->where('db_type', $this->db_type)
                ->where('db_host', $this->db_host)
                ->where('db_port', $this->db_port)
                ->ignore($this->app->id);
        } else {
            $rule_env = Rule::unique('apps')->ignore($this->app->id);
        }

        $rules = [
            'client_id' => 'required|uuid|exists:clients,id',
            'db_name'   => ['required', 'regex:/^[a-z0-9_]+$/', $rule_env],
        ];

        if ($this->use_domain) {
            $rules = array_merge($rules, [
                'domain' => 'required_if:use_domain,true|regex:/^[a-z0-9\.-]$/|unique:apps,domain',
            ]);
        }

        if ($this->use_custom) {
            $rules = array_merge($rules, [
                'redis_host'        => 'required_if:use_custom,true|regex:/^[a-z0-9.-]+$/',
                'redis_port'        => 'required_if:use_custom,true|integer',
                'redis_pass'        => 'nullable|string',
                'db_type'           => 'required_if:use_custom,true|string|in:mysql,pgsql,sqlite',
                'db_host'           => 'required_if:use_custom,true|string',
                'db_port'           => 'required_if:use_custom,true|numeric',
                'super_user'        => 'required_if:use_custom,true|regex:/^[a-zA-Z0-9_]+$/',
                'super_pass'        => 'required_if:use_custom,true|string',
                'db_user'           => 'required_if:use_custom,true|regex:/^[a-zA-Z0-9_]+$/',
                'db_pass'           => 'required_if:use_custom,true|string',
                'cache_driver'      => 'required_if:use_custom,true|in:redis,memcached,file',
                'session_driver'    => 'required_if:use_custom,true|in:redis,memcached,file',
            ]);
        }


        return $rules;
    }
}
