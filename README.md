# Laravel Passport Api Authentication Implementation
 In this project, here i implement laravel passport for api authentication. And also here i show that step by step how to integrate laravel passport and use in a application.

## How to Install and Run the Project

Step-1. ```git clone https://github.com/mamunurrashid1010/Laravel-Passport-Api-Authentication-Implementation.git```<br>
Step-2. ```cd Laravel-Passport-Api-Authentication-Implementation```<br>
Step-3. ```composer install```<br>
Step-4. Copy ```.env.example``` to ```.env``` <br>
Step-5. Create a new database:
Here I'm using my MySQL PHPMyAdmin to create a database.<br>
Step-6. Open ``` .env ``` file and add your database credentials.
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rest_api
DB_USERNAME=root
DB_PASSWORD=
```
Step-7. ```php artisan migrate``` <br>
Step-8. ```php artisan db:seed``` <br>
Step-9. ```php artisan serve``` <br>
You can see the project on ```http://127.0.0.1:8000```

## How to Integrate Laravel Passport 
##### 1. Create new laravel project via composer
```
composer create-project laravel/laravel laravelPassport
```
Go to project directory ```cd laravelPassport``` or open project with IDE.
##### 2. Create a new database
Here I'm using my MySQL PHPMyAdmin to create a database.<br>
Open ``` .env ``` file and add your database credentials.
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_passport
DB_USERNAME=root
DB_PASSWORD=
```
Run migration artisan command
```
php artisan migrate
```

##### 3. Passport Package installation and setup
package installation command run
```
composer require laravel/passport
```

Passport's service provider registers its own database migration directory, so you should migrate your database after installing the package.Run this command-
```
php artisan migrate
```

Next, execute the passport:install artisan command
```
php artisan passport:install
```

Open ```User Model``` remove ```use Laravel\Sanctum\HasApiTokens``` trait, and add
```
use Laravel\Passport\HasApiTokens; //add
 
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; //add
    ....
}
```

Open ```AuthServiceProvider``` from ```App\Providers``` and add
```
use Laravel\Passport\Passport; //add

// In boot function
public function boot()
    {
        Passport::routes(); //add
        ...
    }
```

Next, passport authentication Guard setup. Open ```config/auth.php``` & add
```
// in this guards section
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    // add this api guards
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

###### Passport integration completed.

Now i give an example of creating Rest API with passport authentication.
## How to Create Rest API with passport Authentication
Step-1: Laravel passport integration (we already done).

Step-2: Create a new column (access_token) in ```users table``` for store token.
You can add it manually or follow this step-
```
php artisan make:migration add_access_token_to_users_table --table=users
```

Then open this schema table from ```App\database\migrations\``` and add
```
Schema::table('users', function (Blueprint $table) {
            $table->text('access_token')->nullable(); //add
        });
```

Then run migration artisan command
```
php artisan migrate
```

Step-3: Now create a api route in ```routes\api.php```
```
# user login
Route::post('/userLogin',[userApiController::class,'userLogin']);
```

Step-4: Then create a controller ```userApiController```
```
php artisan make:controller userApiController
```
Then open ```userApiController``` from ```App\Http\Controllers\userApiController.php```<br>
and create a method ``` userLogin```  or add this
``` 
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psy\Util\Json;
use \Illuminate\Support\Facades\Validator;

class userApiController extends Controller
{
    /**
     * userLogin method
     * @param $request->email, $request->password
     * @return Json [message, access_token]
     */
    public function userLogin(Request $request){
        if($request->isMethod('post')){
            $data=$request->all();

            // validation
            $rule=[
                'email'     => 'required|email|exists:users',
                'password'  => 'required',
            ];
            $customMessage=[
                'email.required'    => 'Email is required',
                'email.email'       => 'Email must be a valid mail',
                'email.exists'      => 'Email does not exist',
                'password.required' => 'Password is required',
            ];
            $validation= Validator::make($data,$rule,$customMessage);
            if($validation->fails()){
                return response()->json($validation->errors(),422);
            }

            $user=new User();
            $userDetails= User::where('email',$data['email'])->where('password',$data['password'])->first();
            if($userDetails){
                $access_token=$user->createToken($userDetails->email)->accessToken;
                User::where('email',$userDetails->email)->update(['access_token'=>$access_token]);
                $message="User Successfully Login";
                return response()->json(['message'=>$message,'access_token'=>$access_token],200);
            }
            else{
                $message="User login Fail!";
                return response()->json(['message'=>$message],422);
            }
        }
    }
}

```

Step-5: Then add some dummy data in ```users table```

Step-6: Now test API using Postman
```
POST: http://127.0.0.1:8000/api/userLogin
```

###### Body:form-data
```
key:    email
value:  kamalH@gmail.com
key:    password
value:  12345
```

###### Response samples
```
{
    "message": "User Successfully Login",
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMDNiZDQzNzFhM2ZlMDI5OTFkYWQ0NWUzNTU1YTExY2I2NzEwOTMxOTZiYmMxYzdjY2U1NDE1MGI0ZjEzMDJiOWM1ODc2YzNjN2Q2ZDk2NzciLCJpYXQiOjE2NjYwNzE2MTguNDI3MTkxLCJuYmYiOjE2NjYwNzE2MTguNDI3MTk3LCJleHAiOjE2OTc2MDc2MTguNDE5MDczLCJzdWIiOiIiLCJzY29wZXMiOltdfQ.AA969lDjyNq85UCfxqVCG3lBlda2SwIR5xMgyG5gjMg8nglBx0sehNgZ9Mu2hSTYXP2lAd4kYTpNrIwTpj7OHaBryPEbPKfSb-wCZaBGcMBz5JDVuukhiFX_-CzLiumIv26LL0vR-lSk5BoX6cq7yXP5okn7xZOUXTr63wZFCcy6aUSad73LRLs1Vlkiowkpx-GCjxJMfhMfTRVZzIAOtxwTBaQz5XYGs07PX3SQshUJi2mDtLjNIK-Q3ZgSzGi0DREG1q5xKPXctPQA9DhP3iYl4r02MdK_QxIjwsQRX_98t-CXDW6q0aw40IDwdefybJ9DEEiv-RmTgiKNqJgoIDBJQ0OnBgbs7Mb7AhJ8sAnTvsErsajkJYpbTLVCg1m8Lt7pK1utC5jHHeDK-22ZzuwV7OE_J-3w4UvgnK1lhvT2yN3k6CFqelHbQREdFwFbndo5ZfCCQAWVNVGjloJVmIBtFq8pNrmXgY5qvt1ZXdEfqwjdyx5BHzm-5LrTUMXu4Qbkez0lVGX-w3rdcA2GSO-Hjg_0d1hocWS8B2cYlatvXQFuLx-UXHtmYOfHFOky8iV36puci6zfJLX5NJTkZCFORNfgXjpPjiF_6Fr93dI7VypoqkGi4MwYQqpgriHkvOMjw9Wsg-Zyn2nQhKFKdVqSXX73mgDsqK0nyfhtLd0"
}
```
###### Completed.
