<?php
namespace App\Http\Middlewares;


use App\Utils\Notify;
use App\Models\Dfd;
use App\Models\Etp;
use App\Models\Unit;
use App\Models\User;
use App\Models\Organ;
use App\Models\Sector;
use App\Models\Catalog;
use App\Models\Process;
use App\Models\RiskMap;
use App\Models\Dotation;
use App\Models\Supplier;
use App\Models\Comission;
use App\Models\Demandant;
use App\Models\Ordinator;
use App\Models\Attachment;
use App\Models\CatalogItem;
use App\Models\PriceRecord;
use App\Models\ComissionEnd;
use App\Models\ComissionMember;
use App\Models\CatalogSubCategoryItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;

class Data
{
    /**
     * Obtém o valor do cabeçalho 'X-Custom-Header-Organ'.
     *
     * @return string|null Valor do cabeçalho ou null se não estiver definido.
     */
    private static function getOrgan(): ?string
    {
        return request()->header('X-Custom-Header-Organ');
    }

    /**
     * Salva um modelo fornecido com os parâmetros especificados.
     *
     * @param Model $model Modelo a ser salvo.
     * @param array $params Parâmetros para preenchimento do modelo.
     * @return object Resultado da operação, incluindo código de status e mensagem.
     */
    public static function save(Model $model, array $params)
    {
        $organ = self::getOrgan();
        $data_model = array_merge(['organ' => $organ], $params);

        if (isset($params['id'])) {
            $model = $model->find($params['id']);
            if (!$model) {
                return self::result(404, 'Registro não encontrado.');
            }
        }

        $model->fill($data_model);
        $validate = self::validate($model, $data_model);

        if ($validate) {
            return self::result(403, $validate);
        }

        return $model->save() ? self::result(200) : self::result(500, 'Falha ao gravar dados...');
    }

    /**
     * Busca registros com base nos parâmetros especificados.
     *
     * @param Model $model Modelo a ser buscado.
     * @param array $params Parâmetros para filtros de busca.
     * @param array|null $order Ordem dos resultados.
     * @param array|null $with Relacionamentos para carregar.
     * @param array|null $between Intervalo de dados para filtragem.
     * @param array|null $objects Filtros de objetos JSON.
     * @return Collection|null Coleção de resultados ou null se não houver resultados.
     */
    public static function find(Model $model, array $params = [], ?array $order = null, ?array $with = null, ?array $between = null, ?array $objects = null)
    {
        return self::query($model, $params, $order, $with, $between, $objects)?->get();
    }

    /**
     * Busca um único registro com base nos parâmetros especificados.
     *
     * @param Model $model Modelo a ser buscado.
     * @param array $params Parâmetros para filtros de busca.
     * @param array|null $order Ordem dos resultados.
     * @param array|null $with Relacionamentos para carregar.
     * @param array|null $between Intervalo de dados para filtragem.
     * @param array|null $objects Filtros de objetos JSON.
     * @return Model|null Primeiro resultado ou null se não houver resultados.
     */
    public static function findOne(Model $model, array $params = [], ?array $order = null, ?array $with = null, ?array $between = null, ?array $objects = null)
    {
        return self::query($model, $params, $order, $with, $between, $objects)?->first();
    }

    /**
     * Exclui um registro com base no ID fornecido.
     *
     * @param Model $model Modelo de onde o registro será excluído.
     * @param int $id ID do registro a ser excluído.
     * @return object Resultado da operação de exclusão.
     */
    public static function delete(Model $model, int $id)
    {
        $delete = $model->where(['id' => $id, 'organ' => self::getOrgan()])->delete();

        if ($delete) {
            return self::result(200, 'Registro removido com sucesso...');
        }

        return self::result(500, 'Registro não pode ser apagado, pois é referenciado em outro contexto...');
    }

    /**
     * Constrói uma consulta com base nos parâmetros especificados.
     *
     * @param Model $model Modelo base da consulta.
     * @param array $params Parâmetros para filtros de busca.
     * @param array|null $order Ordem dos resultados.
     * @param array|null $with Relacionamentos para carregar.
     * @param array|null $between Intervalo de dados para filtragem.
     * @param array|null $objects Filtros de objetos JSON.
     * @return Builder|null Consulta construída ou null se não houver critérios.
     */
    public static function query(Model $model, array $params = [], ?array $order = null, ?array $with = null, ?array $between = null, ?array $objects = null)
    {
        $user = Auth::user();
        if (is_null($user) || !($user->profile == User::PRF_ADMIN || !is_null($user->organs))) {
            return null;
        }

        $query = $model->query();

        // Adicionando parâmetros AND e OR
        foreach (['paramsAND' => 'where', 'paramsOR' => 'orWhere'] as $method => $clause) {
            $paramConditions = self::$method($params);
            if (!empty($paramConditions)) {
                $query->where(function ($query) use ($paramConditions, $clause) {
                    foreach ($paramConditions as $prm) {
                        $query->{$clause}($prm->column, $prm->operator, $prm->value);
                    }
                });
            }
        }

        $mods = match (get_class($model)) {
            Attachment::class,
            Catalog::class,
            CatalogItem::class,
            CatalogSubCategoryItem::class,
            Supplier::class,
            Etp::class,
            PriceRecord::class,
            Process::class,
            RiskMap::class => self::paramsGenericOrgan(),

            Comission::class,
            ComissionMember::class,
            ComissionEnd::class,
            Demandant::class,
            Dfd::class,
            Dotation::class,
            Ordinator::class,
            Sector::class => self::paramsGeneric($user),

            Organ::class => self::paramsOrgan(),

            Unit::class => $user->profile > User::PRF_GESTOR ? self::paramsUnit($user) : self::paramsGenericOrgan(),

            default => null
        };

        if ($mods) {
            $query->where(function ($query) use ($mods) {
                foreach ($mods as $mod) {
                    $query->orWhere($mod->column, $mod->operator, $mod->value);
                }
            });
        }

        // Aplicando filtros adicionais
        array_filter([
            $between ? fn() => $query->whereBetween(key($between), current($between)) : null,
            $objects ? fn() => array_walk($objects, fn($value, $key) => $query->whereJsonContains($key, $value)) : null,
            $order ? fn() => $query->orderBy(...$order) : null,
            $with ? fn() => $query->with($with) : null,
        ], fn($clause) => $clause && $clause());

        return $query;
    }

    /**
     * Valida os dados do modelo com base em regras e mensagens definidas.
     *
     * @param Model $model Modelo a ser validado.
     * @param array|null $data Dados para validação.
     * @return string|null Mensagem de erro de validação ou null se estiver válido.
     */
    public static function validate(Model $model, ?array $data = []): ?string
    {
        if (method_exists($model, 'rules') && method_exists($model, 'messages')) {
            $validator = Validator::make($data, $model->rules(), $model->messages());
            if ($validator->fails()) {
                return $validator->errors()->first();
            }
        }

        return null;
    }

    /**
     * Cria um objeto de resultado para as operações de banco de dados.
     *
     * @param int $code Código de status da operação.
     * @param string|null $msg Mensagem opcional para o resultado.
     * @return object Objeto de resultado contendo código, notificação e status.
     */
    private static function result(int $code, ?string $msg = null): object
    {
        return (object) match (true) {
            $code == 200 => ['code' => $code, 'notify' => Notify::success($msg ?? 'Dados salvos com sucesso...'), 'status' => true],
            $code >= 400 && $code < 500 => ['code' => $code, 'notify' => Notify::warning($msg), 'status' => false],
            default => ['code' => $code, 'notify' => Notify::error($msg), 'status' => false]
        };
    }

    /**
     * Retorna parâmetros para filtrar por Organ.
     *
     * @return array Lista de parâmetros de filtro.
     */
    private static function paramsOrgan(): array
    {
        $id = self::getOrgan();
        return [(object) ['column' => 'id', 'operator' => '=', 'value' => $id]];
    }

    /**
     * Retorna parâmetros para filtrar por Unidade.
     *
     * @param $user Usuário autenticado.
     * @return array Lista de parâmetros de filtro.
     */
    private static function paramsUnit($user): array
    {
        return $user->profile > User::PRF_GESTOR
            ? array_map(fn($id) => (object) ['column' => 'id', 'operator' => '=', 'value' => $id], array_column($user->units ?? [], "id"))
            : [(object) ['column' => 'id', 'operator' => '=', 'value' => null]];
    }

    /**
     * Retorna parâmetros genéricos para filtrar por Organ.
     *
     * @return array Lista de parâmetros de filtro.
     */
    private static function paramsGenericOrgan(): array
    {
        $id = self::getOrgan();
        return [(object) ['column' => 'organ', 'operator' => '=', 'value' => $id]];
    }

    /**
     * Retorna parâmetros genéricos para filtrar por Unidade.
     *
     * @param $user Usuário autenticado.
     * @return array Lista de parâmetros de filtro.
     */
    private static function paramsGenericUnit($user): array
    {
        return array_map(fn($id) => (object) ['column' => 'unit', 'operator' => '=', 'value' => $id], array_column($user->units ?? [], "id"));
    }

    /**
     * Retorna parâmetros genéricos para filtrar por Organ e Unidade.
     *
     * @param $user Usuário autenticado.
     * @return array Lista de parâmetros de filtro.
     */
    private static function paramsGeneric($user): array
    {
        return array_merge(self::paramsGenericOrgan(), self::paramsGenericUnit($user));
    }

    /**
     * Retorna condições de filtro para parâmetros AND.
     *
     * @param array|null $params Parâmetros de entrada.
     * @return array Lista de parâmetros filtrados.
     */
    private static function paramsAND(?array $params): array
    {
        return array_filter(array_map(fn($key, $value) => (object) [
            'column' => $value['column'] ?? $key,
            'operator' => $value['operator'] ?? (is_numeric($value) ? '=' : 'LIKE'),
            'value' => $value['value'] ?? $value,
        ], array_keys($params), $params));
    }

    /**
     * Retorna condições de filtro para parâmetros OR.
     *
     * @param array|null $params Parâmetros de entrada.
     * @return array Lista de parâmetros filtrados.
     */
    private static function paramsOR(?array $params): array
    {
        return array_filter(array_map(fn($param) => (object) [
            'column' => $param['column'],
            'operator' => $param['operator'] ?? (is_numeric($param['value']) ? '=' : 'LIKE'),
            'value' => $param['value']
        ], array_filter($params, fn($param) => isset ($param['mode']) && $param['mode'] === 'OR')));
    }
}
