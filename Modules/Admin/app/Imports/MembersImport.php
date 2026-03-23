<?php

namespace Modules\Admin\App\Imports;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class MembersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    private $membroRole;

    /** @var array<int, array{row: int, errors: array<string>}> */
    protected array $failures = [];

    public function __construct()
    {
        $this->membroRole = Role::where('name', 'Jovem')->first() ?? Role::first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Se já existe por email, ignora
        if (User::where('email', $row['email'])->exists()) {
            return null;
        }

        $firstName = $row['nome_abreviado'] ?? explode(' ', $row['nome'])[0] ?? 'Membro';
        $lastName = trim(str_replace($firstName, '', $row['nome'] ?? '')) ?: 'Sobrenome';

        $password = Hash::make('mudar123'); // Senha padrão

        if (isset($row['cpf']) && ! empty($row['cpf'])) {
            $cpfClean = preg_replace('/[^0-9]/', '', $row['cpf']);
            if (User::where('cpf', $row['cpf'])->exists()) {
                return null;
            }
            // Se tiver CPF, a senha pode ser os 6 primeiros dígitos (opcional, vamos manter padrão por segurança)
        }

        return new User([
            'name' => $row['nome'] ?? 'Novo Membro',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $row['email'],
            'cpf' => $row['cpf'] ?? null,
            'phone' => $row['telefone'] ?? null,
            'cellphone' => $row['celular'] ?? null,
            'gender' => $this->mapGender($row['sexo'] ?? ''),
            'marital_status' => $this->mapMaritalStatus($row['estado_civil'] ?? ''),
            'date_of_birth' => $this->parseDate($row['data_nascimento'] ?? null),
            'membership_date' => $this->parseDate($row['data_protesto'] ?? $row['entrada'] ?? null),
            'password' => $password,
            'is_active' => true,
        ]);
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            }

            return \Carbon\Carbon::parse(str_replace('/', '-', $value));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'nome' => 'required',
        ];
    }

    private function mapGender($value)
    {
        $value = strtolower(trim($value));
        if (in_array($value, ['m', 'masculino', 'homem'])) {
            return 'M';
        }
        if (in_array($value, ['f', 'feminino', 'mulher'])) {
            return 'F';
        }

        return 'O';
    }

    private function mapMaritalStatus($value)
    {
        $value = strtolower(trim($value));
        $map = [
            'solteiro' => 'solteiro',
            'solteira' => 'solteiro',
            'casado' => 'casado',
            'casada' => 'casado',
            'divorciado' => 'divorciado',
            'divorciada' => 'divorciado',
            'viuvo' => 'viuvo',
            'viuva' => 'viuvo',
            'uniao estavel' => 'uniao_estavel',
            'uniao_estavel' => 'uniao_estavel',
        ];

        return $map[$value] ?? 'solteiro';
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $row = $failure->row();
            $this->failures[$row] = $this->failures[$row] ?? ['row' => $row, 'errors' => []];
            foreach ($failure->errors() as $error) {
                $this->failures[$row]['errors'][] = $error;
            }
        }
    }

    /** @return array<int, array{row: int, errors: array<string>}> */
    public function getFailures(): array
    {
        return array_values($this->failures);
    }
}
