<?php

namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Uploads
{
    public const UPLOAD_PATH = 'uploads';
    public const UPLOAD_SUCCESS = 0;
    public const UPLOAD_INVALID_TYPE = 9;
    public const STATUS_SUCCESS = 'success';
    public const STATUS_WARNING = 'warning';
    public const STATUS_DANGER = 'danger';

    private Request $request;
    public array $files;
    public array $uploadedFiles;
    public array $result;


    public function __construct(Request $request, array $files)
    {
        $this->request = $request;
        $this->files = $files;
        $this->uploadedFiles = [];
        $this->result = [
            'status' => '',
            'info' => '',
        ];

        $this->upload();
    }

    private function getMessages(): array
    {
        return [
            self::UPLOAD_SUCCESS => 'O arquivo foi enviado com sucesso',
            UPLOAD_ERR_INI_SIZE => 'O arquivo enviado excede a taxa total de transferencia permitida',
            UPLOAD_ERR_FORM_SIZE => 'O arquivo enviado excede o tamanho máximo permitido',
            UPLOAD_ERR_PARTIAL => 'O arquivo selecionado foi apenas parcialmente enviado',
            UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta uma pasta temporária',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao gravar o arquivo no disco',
            UPLOAD_ERR_EXTENSION => 'O servidor impediu o envio do arquivo',
            self::UPLOAD_INVALID_TYPE => 'Tipo de arquivo não aceito!'
        ];
    }

    public static function getValidMimeTypes(): array
    {
        return ['jpeg', 'jpg', 'png', 'pdf', 'bmp', 'svg'];
    }

    private function validate(array $checkfile): bool
    {
        foreach ($checkfile as $file => $options) {
            $hasFile = ($this->request->hasFile($file) && $this->request->file($file)->isValid());
            $upfile = $this->request->$file;

            //upload file no mandatory
            if ($options['nullable']) {
                if ($hasFile) {
                    if (!$this->validateMimes($upfile->extension(), $options['mimes'])) {
                        $this->setResult(self::STATUS_WARNING, self::UPLOAD_INVALID_TYPE);
                        return false;
                    }
                }
            }
            //upload file mandatory
            else {
                if ($hasFile) {
                    if (!$this->validateMimes($upfile->extension(), $options['mimes'])) {
                        $this->setResult(self::STATUS_WARNING, self::UPLOAD_INVALID_TYPE);
                        return false;
                    }
                } else {
                    $this->setResult(self::STATUS_WARNING, UPLOAD_ERR_NO_FILE);
                    return false;
                }
            }
        }

        return true;
    }

    public function upload(): void
    {
        foreach ($this->files as $file => $options) {
            $upfile = $this->request->$file;
            if ($this->validate([$file => $options]) && $upfile != null) {
                try {
                    $namefile = md5($upfile->getClientOriginalName() . strtotime('now')) . '.' . $upfile->extension();
                    $upfile->move(public_path(self::UPLOAD_PATH), $namefile);
                    $this->uploadedFiles[$file] = $namefile;
                    $this->setResult(self::STATUS_SUCCESS, self::UPLOAD_SUCCESS);
                } catch (\Throwable $e) {
                    Log::error('Falha ao realizar upload: ' . $e->getMessage());
                    $this->setResult(self::STATUS_WARNING, $upfile->getError());
                }
            }
        }
    }

    public function fails(): bool
    {
        return $this->getStatus() != null && $this->getStatus() != self::STATUS_SUCCESS;
    }

    public function mergeUploads(array $values): array
    {
        return !$this->fails()
            ? array_merge($values, $this->uploadedFiles)
            : $values;
    }

    private function setResult(string $status, int $info): void
    {
        $this->result['status'] = $status;
        $this->result['info'] = $this->getMessages()[$info];
    }

    private function validateMimes(string $extension, array $mimes): bool
    {
        return in_array($extension, $mimes ?? $this->getValidMimeTypes());
    }

    public function getStatus(): string
    {
        return $this->result['status'];
    }

    public function getInfo(): string
    {
        return $this->result['info'];
    }

    public function remove(?string $filename = null): void
    {
        if(!is_null($filename)) {
            unlink(public_path(self::UPLOAD_PATH) . $filename);
        }
    }
}