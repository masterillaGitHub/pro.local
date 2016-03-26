<?php
namespace Core;

class AutoLoader
{
    /**
     * Ассоциативный массив. Ключи содержат префикс пространства имён,
     * значение — массив базовых директорий для классов в этом пространстве имён.
     *
     * @var array
     */
    public $prefixes = [];

    /**
     * Регистрирует загрузчик в стеке загрузчиков SPL.
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * Добавляет базовую директорию к префиксу пространства имён.
     *
     * @param string $prefix Префикс пространства имён.
     * @param string $base_dir Базовая директория для файлов классов из пространства имён.
     * @param bool $prepend Если true, добавить базовую директорию в начало стека.
     * В этом случае она будет проверяться первой.
     * @return void
     */
    public function addNamespace($prefix, $base_dir, $prepend = false)
    {
        // нормализуем префикс пространства имён
        $prefix = trim($prefix, '\\') . '\\';

        // нормализуем базовую директорию так, чтобы всегда присутствовал разделитель в конце
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        // инициализируем массив префиксов пространства имён
        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = [];
        }

        // сохраняем базовую директорию для префикса пространства имён
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
    }

    /**
     * Загружает файл для заданного имени класса.
     *
     * @param string $class Абсолютное имя класса.
     * @return mixed Если получилось, полное имя файла. Иначе — false.
     */
    public function loadClass($class)
    {
        // текущий префикс пространства имён


        $prefix = $class;
        // для определения имени файла обходим пространства имён из абсолютного
        // имени класса в обратном порядке
        // deBug($prefix, '$prefix', 0, 0, 0, __FILE__, __LINE__, __METHOD__); // [debugBacktrace], dump, echo, stop
        // deBug(strrpos($prefix, '\\'), 'strrpos($prefix, \'\\\')', 0, 0, 0, __FILE__, __LINE__, __METHOD__); // [debugBacktrace], dump, echo, stop
        while (false !== $pos = strrpos($prefix, '\\')) {

            // сохраняем завершающий разделитель пространства имён в префиксе
            $prefix = substr($class, 0, $pos + 1);
            // deBug($prefix, '$prefixAfter', 0, 0, 0, __FILE__, __LINE__, __METHOD__); // [debugBacktrace], dump, echo, stop

            // всё оставшееся — относительное имя класса
            $relative_class = substr($class, $pos + 1);
            // deBug($relative_class, '$relative_class', 0, 0, 0, __FILE__, __LINE__, __METHOD__); // [debugBacktrace], dump, echo, stop
            // пробуем загрузить соответсвующий префиксу и относительному имени класса файл
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            // deBug($mapped_file, '$mapped_file', 1, 0, 0, __FILE__, __LINE__, __METHOD__); // [debugBacktrace], dump, echo, stop
            if ($mapped_file) {
                return $mapped_file;
            }

            // убираем завершающий разделитель пространства имён для следующей итерации strrpos()
            $prefix = rtrim($prefix, '\\');
        }

        // файл так и не был найден
        return false;
    }

    /**
     * Загружает файл, соответствующий префиксу пространства имён и относительному имени класса.
     *
     * @param string $prefix Префикс пространства имён.
     * @param string $relative_class Относительное имя класса.
     * @return mixed false если файл не был загружен. Иначе имя загруженного файла.
     */
    protected function loadMappedFile($prefix, $relative_class)
    {
        // есть ли у этого префикса пространства имён какие-либо базовые директории?
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        // ищем префикс в базовых директориях
        foreach ($this->prefixes[$prefix] as $base_dir) {

            // заменяем префикс базовой директорией,
            // заменяем разделители пространства имён на разделители директорий,
            // к относительному имени класса добавляем .php
            $file = $base_dir.str_replace('\\', '/', $relative_class).'.php';
            // если файл существует, загружаем его
            if ($this->requireFile($file)) {
                // ура, получилось
                return $file;
            }
        }

        // файл так и не был найден
        return false;
    }

    /**
     * Если файл существует, загружеаем его.
     *
     * @param string $file файл для загрузки.
     * @return bool true, если файл существует, false — если нет.
     */
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}
