<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Docente extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'codigo_docente',
        'carnet_identidad',
        'telefono',
        'titulo',
        'facultad',
        'estado',
        'fecha_contratacion',
        'qr_token',
    ];

    /**
     * Los atributos protegidos contra mass assignment.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     * Genera el siguiente código de docente disponible.
     * Comienza desde 100 y busca el siguiente número disponible.
     *
     * @return string
     */
    public static function generateNextCodigo(): string
    {
        // Obtener el último código docente (ordenado numéricamente)
        $lastDocente = self::orderByRaw('CAST(codigo_docente AS INTEGER) DESC')->first();

        if (!$lastDocente) {
            // Si no hay docentes, empezar desde 100
            return '100';
        }

        // Incrementar el último código
        $nextCodigo = (int)$lastDocente->codigo_docente + 1;

        // Si el siguiente código es menor que 100, comenzar desde 100
        return (string)max($nextCodigo, 100);
    }

    /**
     * Obtiene el usuario (cuenta) al que pertenece este perfil de docente.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene todos los grupos asignados al docente.
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    /**
 * Obtiene todos los títulos asociados al docente.
 */
public function titulos()
{
    return $this->hasMany(Titulo::class);
}

    /**
     * Genera un token QR único y seguro para este docente.
     * Este token es permanente y se usa para firmar códigos QR.
     */
    public function generateQrToken(): string
    {
        if (!$this->qr_token) {
            $this->qr_token = hash('sha256', Str::random(64) . $this->codigo_docente . now());
            $this->save();
        }

        return $this->qr_token;
    }

    /**
     * Genera los datos del código QR con firma de seguridad.
     * Incluye: código_docente, docente_id, timestamp y firma HMAC.
     *
     * @return string JSON string para codificar en QR
     */
    public function generateQrData(): string
    {
        $this->generateQrToken();

        $data = [
            'docente_id' => $this->id,
            'codigo_docente' => $this->codigo_docente,
            'timestamp' => now()->timestamp,
        ];

        // Firma HMAC para validar autenticidad
        $data['signature'] = hash_hmac('sha256',
            $data['docente_id'] . $data['codigo_docente'] . $data['timestamp'],
            $this->qr_token
        );

        return json_encode($data);
    }

    /**
     * Valida si los datos del QR escaneado son válidos y están dentro del tiempo permitido.
     *
     * @param string $qrData Datos del QR escaneado
     * @param int $validMinutes Minutos de validez del QR (por defecto 5)
     * @return array ['valid' => bool, 'message' => string, 'data' => array|null]
     */
    public static function validateQrData(string $qrData, int $validMinutes = 5): array
    {
        try {
            $data = json_decode($qrData, true);

            if (!isset($data['docente_id'], $data['codigo_docente'], $data['timestamp'], $data['signature'])) {
                return ['valid' => false, 'message' => 'QR inválido o corrupto', 'data' => null];
            }

            // Buscar docente por ID (más seguro)
            $docente = self::find($data['docente_id']);

            if (!$docente || !$docente->qr_token || $docente->codigo_docente !== $data['codigo_docente']) {
                return ['valid' => false, 'message' => 'Docente no encontrado o QR no coincide', 'data' => null];
            }

            // Verificar firma (ahora incluye docente_id)
            $expectedSignature = hash_hmac('sha256',
                $data['docente_id'] . $data['codigo_docente'] . $data['timestamp'],
                $docente->qr_token
            );

            if (!hash_equals($expectedSignature, $data['signature'])) {
                return ['valid' => false, 'message' => 'QR falsificado o inválido', 'data' => null];
            }

            // Verificar ventana de tiempo (el QR se regenera cada vez pero validamos tiempo razonable)
            $qrTime = \Carbon\Carbon::createFromTimestamp($data['timestamp']);
            $diffMinutes = abs(now()->diffInMinutes($qrTime));

            if ($diffMinutes > $validMinutes) {
                return ['valid' => false, 'message' => 'QR expirado. Por favor, recarga el código.', 'data' => null];
            }

            return [
                'valid' => true,
                'message' => 'QR válido',
                'data' => [
                    'docente' => $docente,
                    'docente_id' => $docente->id,
                    'timestamp' => $qrTime
                ]
            ];

        } catch (\Exception $e) {
            return ['valid' => false, 'message' => 'Error al validar QR: ' . $e->getMessage(), 'data' => null];
        }
    }

    /**
     * Valida la firma de un array de datos QR.
     * Método auxiliar para simplificar la validación.
     *
     * @param array $data Datos decodificados del QR
     * @param int $validMinutes Minutos de validez
     * @return bool
     */
    public function validateQrSignature(array $data, int $validMinutes = 5): bool
    {
        if (!isset($data['codigo_docente'], $data['timestamp'], $data['signature'])) {
            return false;
        }

        // Verificar que el código docente coincida
        if ($data['codigo_docente'] !== $this->codigo_docente) {
            return false;
        }

        // Verificar firma
        $expectedSignature = hash_hmac('sha256',
            $data['codigo_docente'] . $data['timestamp'],
            $this->qr_token ?? ''
        );

        if (!hash_equals($expectedSignature, $data['signature'])) {
            return false;
        }

        // Verificar ventana de tiempo
        $qrTime = \Carbon\Carbon::createFromTimestamp($data['timestamp']);
        $diffMinutes = abs(now()->diffInMinutes($qrTime));

        return $diffMinutes <= $validMinutes;
    }
}
