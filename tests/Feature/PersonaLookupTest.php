<?php

namespace Tests\Feature;

use App\Models\Persona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonaLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_person_by_document_type_and_number(): void
    {
        Persona::create([
            'n_documento' => '77665544',
            'tipo_documento' => 'DNI',
            'nombre_completo' => 'María Pérez',
            'domicilio' => 'Av. Principal 123',
            'referencia' => 'Frente al parque',
            'correo' => 'maria@example.com',
            'telefono' => '987654321',
            'fecha_nacimiento' => '1990-05-10',
            'activo' => true,
        ]);

        $response = $this->getJson('/api/personas/buscar?tipo_documento=DNI&n_documento=77665544');

        $response
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.nombre_completo', 'María Pérez')
            ->assertJsonPath('data.correo', 'maria@example.com');
    }

    public function test_it_returns_not_found_when_the_person_does_not_exist(): void
    {
        $this->getJson('/api/personas/buscar?tipo_documento=DNI&n_documento=00000000')
            ->assertNotFound()
            ->assertJsonPath('status', 'error');
    }

    public function test_it_validates_the_lookup_parameters(): void
    {
        $this->getJson('/api/personas/buscar?tipo_documento=DNI')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['n_documento']);
    }
}
