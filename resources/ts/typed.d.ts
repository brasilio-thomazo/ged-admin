declare global {
  interface Tab {
    title: string
    slot: any
    type: string
    icon?: string
  }

  interface TabSlot {
    create: { slot: any; icon?: string }
    edit: { slot: any; icon?: string }
    show: { slot?: any; icon?: string }
    list: { slot: any; icon?: string }
    error: { slot?: any; icon?: string }
  }

  type SlotType = 'create' | 'edit' | 'show' | 'list' | 'error'

  /**
   * Authentication type define
   */
  interface AuthRequest {
    username: string
    password: string
    remember?: boolean
  }

  interface AuthError {
    message?: string
    errors?: {
      username?: string[]
      password?: string[]
    }
  }

  /**
   * Privilege type deine
   */
  type Permission = 'r' | 'rw'

  interface Privilege {
    group?: Permission
    user?: Permission
    client?: Permission
    app?: Permission
    authorities: string[]
  }

  interface PrivilegeRequest {
    group?: Permission
    user?: Permission
    client?: Permission
    app?: Permission
  }

  /**
   * Group type define
   */
  interface Group {
    id: number
    name: string
    is_admin: boolean
    privilege: Privilege
    created_at: string
    updated_at: string
  }

  interface GroupRequest {
    name: string
    privilege: PrivilegeRequest
  }

  interface GroupError {
    message?: string
    errors?: {
      name?: string[]
      privilege?: string[]
    }
  }
  /**
   * User type define
   */
  interface User {
    id: string
    name: string
    identity: string
    role: string
    phone: string
    email: string
    username: string
    groups: Group[]
    created_at: string
    updated_at: string
  }

  interface UserRequest {
    name: string
    identity: string
    role: string
    phone: string
    email: string
    username: string
    password: string
    password_confirmation: string
    groups: number[]
  }

  interface UserError {
    message?: string
    errors?: {
      name?: string[]
      identity?: string[]
      role?: string[]
      phone?: string[]
      email?: string[]
      username?: string[]
      password?: string[]
      groups?: sring[]
    }
  }
  /**
   * Client type define
   */
  interface Client {
    id: string
    name: string
    identity: string
    email: string
    phone: string
    scope: 'client' | 'agent'
    manager: string
    role: string
    created_at: string
    updated_at: string
  }

  interface ClientRequest {
    name: string
    identity: string
    email: string
    phone: string
    scope: 'client' | 'agent'
    manager: string
    role: string
  }

  interface ClientError {
    message?: string
    errors?: {
      name?: string[]
      identity?: string[]
      email?: string[]
      phone?: string[]
      scope?: string[]
      manager?: string[]
      role?: string[]
    }
  }
  /**
   * Type app define
   */
  interface App {
    id: string
    client_id: string
    application: 0 | 1
    path: string
    subdomain: string
    use_domain: boolean
    domain?: string
    use_custom: boolean
    redis_host?: string
    redis_port?: number
    memcached_host?: string
    db_type?: 'mysql' | 'pgsql' | 'sqlite'
    db_host?: string
    db_port?: number
    db_name: string
    cache_driver?: 'redis' | 'memcached' | 'file'
    session_driver?: 'redis' | 'memcached' | 'file'
    installed_at?: string
    started_at?: string
    created_at: string
    updated_at: string
    grpc_port: number
    client: Client
  }
  interface AppRequest {
    application: 0 | 1
    client_id: string
    path: string
    domain?: string
    redis_host?: string
    redis_port?: number
    redis_pass?: string
    memcached_host?: string
    db_type?: 'mysql' | 'pgsql' | 'sqlite'
    db_host?: string
    db_port?: number
    super_user?: string
    super_pass?: string
    db_name: string
    db_user?: string
    db_pass?: string
    cache_driver?: 'redis' | 'memcached' | 'file'
    session_driver?: 'redis' | 'memcached' | 'file'
    started_at?: string
    installed_at?: string
    use_custom: boolean
    use_domain: boolean
  }

  interface AppError {
    message?: string
    errors?: {
      application?: string[]
      client_id?: string[]
      path?: string[]
      domain?: string[]
      redis_host?: string[]
      redis_port?: string[]
      redis_pass?: string[]
      memcached_host?: string[]
      db_type?: string[]
      db_host?: string[]
      db_port?: string[]
      db_name?: string[]
      super_user?: string[]
      super_pass?: string[]
      db_user?: string[]
      db_pass?: string[]
      cache_driver?: string[]
      session_driver?: string[]
      started_at?: string[]
      installed_at?: string[]
    }
  }
}

export default {}
