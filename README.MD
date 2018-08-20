# [Selectel](https://selectel.com/) CDN Storage 

Optional: Laravel 5.* storage

## Setup Selectel official site
- Register at Selectel
- Create container in Selectel Storage (private access is recommended)
- Create user for safety use
- Grant access with custom access permissions to container(read/write)

## Configure ENV
Add to $_ENV (Laravel: `.env` file)
```sh
SELECTEL_URL=https://auth.selcdn.ru/
SELECTEL_USERNAME=00000_some_user
SELECTEL_PASSWORD=___PASSWORD___
```

### Optional:
For safe use private documents - prepare TEMP_URL_SECRET
Reason: access to private file with temporary key

Add to $_ENV (Laravel: `.env` file)
```sh
SELECTEL_TEMP_URL_SECRET=___ANY_SAFE_STRING_AS_SECRET___
```
preset ___ANY_SAFE_STRING_AS_SECRET___ to CDN
```bash 
#put username and password
curl -i -X POST https://api.selcdn.ru/v2.0/tokens -H 'Content-type: application/json' -d '{"auth": {"passwordCredentials": {"username": "00000_some_user", "password": "___PASSWORD___"}}}'
#put selectel_id, ___TOKEN___ and ___ANY_SAFE_STRING_AS_SECRET___
curl -i -X POST https://api.selcdn.ru/v1/SEL_00000 -H "X-Auth-Token: ___TOKEN___" -H "X-Account-Meta-Temp-URL-Key: ___ANY_SAFE_STRING_AS_SECRET___"
```

## Configure APP
Laravel: Add to `disks` section in `config/filesystems.php`:

```php
   'some_named_container' => [
        'driver' => 'selectel',
        'serviceName'  => env('SELECTEL_SERVICE_NAME'),
        'username' => env('SELECTEL_USERNAME'),
        'password' => env('SELECTEL_PASSWORD'),
        'endpoint' => env('SELECTEL_URL'),
        'container' => env('FS_NS_DEFAULT', 'default'),
        'tenantId'     => env('SELECTEL_TENANT_ID'),
        'tenantName'   => env('SELECTEL_TENANT_NAME'),
        'region'        => env('SELECTEL_REGION'),
#        'cache' => [
#            'store' => env('SELECTEL_CACHE_DRIVER')
#                ?? env('CACHE_DRIVER')
#                ?? 'redis',
#            'expire' => env('SELECTEL_CACHE_EXPIRE', 600),
#            'prefix' => 'cache-selectel-'.env('FS_NS_DEFAULT', 'default'),
#        ],
    ],
```

#### Legacy
For Laravel version less then 5.5 (not need on actual version)
Add to service_provider section in `config/app.php`:
```php
BAGArt\SelectelStorage\SelectelServiceProvider::class,
```

# Laravel use
```php
    dump([
        'put' => Storage::disk('some_named_container)->put('test_file_name', 'some test content')),
        'get' => Storage::disk('some_named_container)->get('test_file_name')
        'delete' =>  Storage::disk('some_named_container)->delete('test_file_name'),
    ]);
```

## Native use
```php
    //example in configure section
    $config_selectel =  config('filesystem.disks.some_named_container');
    
    $disk = new Filesystem(
        new SelectelAdapter(
            (new SelectelOpenStack(
                $config_selectel['endpoint'],
                $config_selectel
            ))
                ->buildContainer()
        )
    );
```