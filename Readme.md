### Laravel Base Repository

A clean, reusable Base Repository class for Laravel apps that helps you organize data access logic using DTOs (Data Transfer Objects) and follow best practices like:

- Separation of concerns
- Reusable query logic
- Type-safe input/output
- Easy extension for model-specific behavior

### Installation

You can install the package via Composer:

```bash
composer require vkoori/laravel-repository
```

### Usage

1. create dto based on [this document](https://github.com/vkoori/laravel-model-dto)
2. Create a Model-Specific Repository

```php
/**
 * @extends BaseRepository<User, UserDTO>
 */
class UserRepository extends BaseRepository
{
    protected function getModel(): User
    {
        return new User();
    }

    protected function getDTO(): UserDTO
    {
        return new UserDTO();
    }
}
```
3. Use It in Your Code

```php
$userRepo = new UserRepository();

// Create
$userDTO = (new UserDTO())->setName('John')->setEmail('john@example.com')->setActive(true);
$user = $userRepo->create($userDTO);

// Get all
$users = $userRepo->get();

// Searching and pagination
$activeDTO = (new UserDTO())->setActive(true);
$user = $userRepo->paginate($activeDTO);

// Find by ID
$user = $userRepo->findById(1);

// Load Relations
$user = $userRepo->findByIdOrFail(1, ['posts']);

// Update
$updateDto = (new UserDTO())->setActive(false);
$user = $userRepo->update(1, $updateDto);

// Delete
$userRepo->deleteById(1);

// Batch insert
$values = [
    (new UserDTO())->setName('Alice')->setEmail('alice@example.com'),
    (new UserDTO())->setName('Bob')->setEmail('bob@example.com'),
];
$userRepo->batchInsert($values);
```

### Available Methods

Here’s a list of all available methods in [BaseRepositoryInterface](src/BaseRepositoryInterface.php)
