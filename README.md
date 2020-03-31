# laravel-route-annotation

最近在laravel项目中，遇到了显式路由和隐式之争。 显式路由太杂乱，要么文件里面写很多条路由，要么分成很多文件，不是很爽。而隐式路由，因为需要实现一套自己项目的内部逻辑，增加一定的上手成本，还有可能导致框架本身的route:cache使用不了  

为了能够更好的解决路由这个问题，尝试用reflection机制模拟了一个sping的注解写法，一方面不用谢配置文件，第二方面可以很好的兼容route:cache，开发运行两不误  

暂时实现了对类的@RequestMapping(value="user", prefix="system", middleware="auth")功能，参数均为可选，如果只有一个value 可以直接用 @RequestMapping("user")  

对于函数实现了 @PostMapping, @GetMapping, @PutMapping, @AnyMapping, @PatchMapping, @DeleteMapping, @OptionsMapping， 参数与@RequestMapping一致, 不同点在于value为必填，不写会异常  

支持@GetMapping("/detail/{id}") 这种动态参数的模式，目前尚不支持domain resource, where, namespace, only 等功能，后面如果项目有需要的话进行更新


##安装使用

###Route  

Step 1.下载源码
```shell script
    git clone https://github.com/lynxcat/laravel-route-annotation.git your path
```
Step 2.修改项目composer.json,在对应的项增加以下内容
```json
    {
        "require": {
            "lynxcat/laravel-route-annotation": "*"
        },
        "repositories": {
            "lynxcat": {
                "type": "path",
                "url": "your path"
            }
        }
    }
```

Step 3.在控制器中使用注解
```php
    
    /**
     * Class UserController
     * @package App\Http\Controllers
     * @RequestMapping("users")
     */
    class UserController extends Controller
    {
    
        /**
         * 用户列表
         * @GetMapping(value="/list", middleware={"auth"})
         */
        public function index()
        {
            return "hello, user list";
        }
    
        /**
         * 用户编辑
         *
         * @GetMapping("/edit/{id}")
         * @PostMapping("/eidt/{id}", middleware={"auth:eidt-user"})
         */
        public function eidt(int $id) {
           
        }
    
    }

```

浏览器访问 http://your_host/users/list 就可以看到结果了


###Service  


Step 1.在类中加上注解
```php
/**
 * Class UserServiceImpl
 * @package App\Services
 *
 * @Service
 */
class UserServiceImpl implements UserInterface {

}
```

Step 2.可以直接使用
```php
use App\Contracts\UserInterface as User;
use Illuminate\Http\Request;

/**
 * Class UserController
 * @package App\Http\Controllers
 * @RequestMapping("users")
 */
class UserController extends Controller
{
    protected $user;
    protected $request;

    public function __construct(Request $request, User $user)
    {
        $this->request = $request;
        $this->user = $user;
    }
}
```

  
另外扩展包自带了两个命令 
```shell script
    php artisan annotation:cache #生成缓存文件
    php artisan annotation:clear #清除缓存文件
```
缓存文件放在 /vendor/lynxcat/laravel-route-annotation/src/Cache/中，可以用于对比生成的route以及service是否正确。

生产环境建议使用 php artisan annotation:cache; php artisan route:cache 可以提升文件读取效率

