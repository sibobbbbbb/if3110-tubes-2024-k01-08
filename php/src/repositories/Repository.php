<?

namespace src\repositories;

use src\database\Database;

/**
 * Base Class Repository
 */
abstract class Repository
{
    // Dependency injection
    protected Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }
}
