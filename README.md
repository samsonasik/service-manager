# Service Manager

This is a prototype for a new service manager for ZFx.

```php
class UserController
{
  /**
   * @Inject
   */
  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }
}
```

```php
/**
 * @CreateWith(factory="User\Factory\UserServiceFactory", shared=true)
 */
class UserService
{

}
```


1. On demande un objet
2. Si une fabrique existe, alors celle-ci est utilisée pour construire l'objet.
3. Autrement, on le passe à un "resolver", par exemple Reflection:
  3.1. On vérifie si le constructeur a l'annotation "Inject". Si non, on s'arrête, sinon on passe en 3.2
  3.2. Pour chaque paramètre, on vérifie si il dispose d'une annotation "CreateWith".
    3.2.1. Si il y en a une, alors on utilise la fabrique indiquée
    3.2.2. Sinon on vérifie si il y a au moins un paramètre obligatoire, si oui => récursion, sinon => invokable factory
