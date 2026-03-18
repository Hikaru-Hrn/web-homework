<?php

abstract class Worker implements WorkerInterface
{
    use LogMassage;

    // Фиксированные значения
    public string $name;
    public int $salary;
    public string $bankAccount;

    // Конструктор
    public function __construct($name, $salary, $bankAccount)
    {
        $this->name = $name;
        $this->salary = $salary;
        $this->bankAccount = $bankAccount;
        $this->logAction("Создан сотрудник {$this->name}");
    }

    // Геттеры/Сеттеры
    public function getName(): string
    {
        return $this->name;
    }
    public function setName($newName)
    {
        $this->logAction("Имя сотрудника {$this->name} заменено на {$newName}");
        $this->name = $newName;
    }
    public function getSalary(): int
    {
        return $this->salary;
    }
    public function setSalary($newSalary)
    {
        $this->logAction(
            "Зарплата сотрудника {$this->name} заменена с {$this->salary} на {$newSalary}",
        );
        $this->salary = $newSalary;
    }
    public function getBankAccount(): string
    {
        return $this->bankAccount;
    }
    public function setBankAccount($newBankAccount)
    {
        $this->logAction(
            "Банковский счет сотрудника {$this->name} заменен с {$this->bankAccount} на {$newBankAccount}",
        );
        $this->bankAccount = $newBankAccount;
    }
}

// Интерфейс
interface WorkerInterface
{
    public function whoAmI();
}

// Трейт
trait LogMassage
{
    public function logAction($action)
    {
        echo "<br><i>[LOG]: $action</i><br>";
    }
}

// Класс Developer наследуемый от Worker
class Developer extends Worker
{
    public function whoAmI()
    {
        echo "I am developer.<br>";
    }
}

// Класс Robot реализующий интерфейс
class Robot implements WorkerInterface
{
    use LogMassage;
    public function whoAmI()
    {
        echo "I am robot";
        $this->logAction("Робот начал работу");
    }
}

// Объект класса
$developer1 = new Developer("Jamal", 76000, "8843924");
// Вызов методов
$developer1->whoAmI();
echo "Зарплата сотрудника {$developer1->getName()} на сумму {$developer1->getSalary()} отправлена на счет {$developer1->getBankAccount()}<br>";
$robot1 = new Robot();
$robot1->whoAmI();
?>
