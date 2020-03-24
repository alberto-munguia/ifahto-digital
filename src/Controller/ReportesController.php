<?php

namespace App\Controller;

use Cake\I18n\Number;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Font;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ReportesController extends AppController
{
    /**
     * Genera el reporte de proyectos
     */
    public function proyectos()
    {
        $this->loadModel('Proyectos');

        $pathname    = WWW_ROOT . 'files' . DS;
        $filename    = $pathname . 'templates' . DS . 'template-proyectos.xlsx';
        $spreadsheet = IOFactory::load($filename);
        $worksheet   = $spreadsheet->getSheetByName('Proyectos');
        $counter     = 2;
        $proyectos   = $this->Proyectos
            ->find()
            ->contain([
                'Clientes',
                'Ciudades.EntidadFederativas',
                'Marcas',
                'TipoClientes',
                'TipoServicios',
            ])
            ->where(['Proyectos.estado' => 1]);

        foreach ($proyectos as $proyectoObj) {
            $fechaEntrega = !empty($proyectoObj->entrega)
                ? $proyectoObj->entrega->format('d-m-Y')
                : '';

            $fechaFinalizacion = !empty($proyectoObj->finalizacion)
                ? $proyectoObj->finalizacion->format('d-m-Y')
                : '';

            $worksheet->setCellValue('A' . $counter, $proyectoObj->clave);
            $worksheet->setCellValue('B' . $counter, $proyectoObj->nombre);
            $worksheet->setCellValue('C' . $counter, $proyectoObj->descripcion);
            $worksheet->setCellValue('D' . $counter, $proyectoObj->cliente->nombre);
            $worksheet->setCellValue('E' . $counter, $proyectoObj->marca->nombre);
            $worksheet->setCellValue('F' . $counter, $proyectoObj->fecha_autorizacion->format('d-m-Y'));
            $worksheet->setCellValue('G' . $counter, $fechaEntrega);
            $worksheet->setCellValue('H' . $counter, $fechaFinalizacion);
            $worksheet->setCellValue('I' . $counter, $proyectoObj->ciudad->nombre);
            $worksheet->setCellValue('J' . $counter, $proyectoObj->ciudad->entidad_federativa->nombre);
            $worksheet->setCellValue('K' . $counter, $proyectoObj->tipo_cliente->nombre);
            $worksheet->setCellValue('L' . $counter, $proyectoObj->tipo_servicio->nombre);
            $worksheet->setCellValue('M' . $counter, $proyectoObj->estatus);
            $worksheet->setCellValue('N' . $counter, Number::currency($proyectoObj->monto, 'USD'));

            $counter++;
        }

        $newName = 'reporte-proyectos.xlsx';
        $writer  = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($pathname . $newName);

        return $this->response->withFile($pathname . $newName);
    }

    /**
     * Genera el reporte de los costos de cada recurso por proyecto.
     *
     * @param integer $id Id del proyecto
     */
    public function timesheetProyectos($id = null)
    {
        if (is_null($id) || empty($id)) {
            throw new \Cake\Http\Exception\BadRequestException('Método no encontrado');
        }

        $this->loadModel('Usuarios');
        $this->loadModel('Proyectos');
        $this->loadModel('Gastos');

        $pathname    = WWW_ROOT . 'files' . DS;
        $filename    = $pathname . 'templates' . DS . 'template-proyecto-timesheet.xlsx';
        $spreadsheet = IOFactory::load($filename);
        $worksheet   = $spreadsheet->getSheetByName('Timesheet');
        $counter     = 8;
        $proyectoObj = $this->Proyectos
            ->find()
            ->contain([
                'Clientes',
                'Ciudades.EntidadFederativas',
                'Marcas',
                'TipoClientes',
                'TipoServicios',
            ])
            ->where([
                'Proyectos.id'     => $id,
                'Proyectos.estado' => 1,
            ])
            ->first();

        $worksheet->setCellValue('C3', $proyectoObj->nombre);
        $worksheet->setCellValue('C4', $proyectoObj->cliente->nombre);
        $worksheet->setCellValue('C5', $proyectoObj->marca->nombre);
        $worksheet->setCellValue('H3', $proyectoObj->clave);
        $worksheet->setCellValue('H4', $proyectoObj->estatus);

        $timesheets = $this->Proyectos->Timesheets
            ->find('all')
            ->select(['total' => 'sum(total)', 'usuario_id', 'mes_anio' => 'date_format(fecha, "%Y-%m")'])
            ->where(['Timesheets.proyecto_id' => $proyectoObj->id])
            ->group(['mes_anio', 'usuario_id'])
            ->all();

        $data = [];

        foreach ($timesheets as $timesheetObj) {
            $mesAnio    = explode('-', $timesheetObj->mes_anio);
            $usuarioObj = $this->Usuarios->get($timesheetObj->usuario_id);

            $data[$timesheetObj->mes_anio][] = [
                'usuario' => $usuarioObj->nombre_completo,
                'total'   => $timesheetObj->total,
                'costo'   => !empty($usuarioObj->costo_hora) ? $usuarioObj->costo_hora : '0',
            ];
        }

        foreach ($data as $key => $value) {
            foreach ($value as $timesheet) {
                $costoHora = $timesheet['costo'] > 0
                    ? $timesheet['costo'] * $timesheet['total']
                    : $timesheet['costo'];

                $worksheet->setCellValue('A' . $counter, $timesheet['usuario']);
                $worksheet->setCellValue('F' . $counter, $timesheet['total']);
                $worksheet->setCellValue('G' . $counter, $key);
                $worksheet->setCellValue('H' . $counter, $timesheet['costo']);
                $worksheet->setCellValue('I' . $counter, $costoHora);

                $worksheet->mergeCells('A' . $counter . ':E' . $counter);
                $counter++;
            }

            unset($data[$key]);
        }

        $counter = $counter + 5;
        $gastos  = $this->Gastos
            ->find()
            ->contain([
                'Proveedores',
                'TipoGastos',
            ])
            ->where(['Gastos.proyecto_id' => $id]);

        $worksheet->setCellValue('A' . $counter, 'GASTOS');
        $worksheet->mergeCells('A' . $counter . ':M' . $counter);
        $worksheet->getStyle('A' . $counter)->getFont()->setBold(true);
        $worksheet->getStyle('A' . $counter . ':M' . $counter)->getAlignment()->setHorizontal('center');
        $counter = $counter + 2;

        $worksheet->setCellValue('A' . $counter, 'TIPO DE GASTO');
        $worksheet->setCellValue('D' . $counter, 'PROVEEDOR');
        $worksheet->setCellValue('G' . $counter, 'DESCRIPCIÓN');
        $worksheet->setCellValue('K' . $counter, 'IMPORTE');
        $worksheet->setCellValue('L' . $counter, 'FECHA');
        $worksheet->setCellValue('M' . $counter, 'USUARIO RELACIONADO');

        $worksheet->mergeCells('A' . $counter . ':C' . $counter);
        $worksheet->mergeCells('D' . $counter . ':F' . $counter);
        $worksheet->mergeCells('G' . $counter . ':J' . $counter);
        $worksheet->getStyle('A' . $counter . ':M' . $counter)->getFont()->setBold(true);
        $worksheet->getStyle('A' . $counter . ':M' . $counter)->getAlignment()->setHorizontal('center');

        $counter++;

        foreach ($gastos as $gastoObj) {
            $usuarioObj = $this->Usuarios->get($gastoObj->recurso_id);

            $worksheet->setCellValue('A' . $counter, $gastoObj->tipo_gasto->nombre);
            $worksheet->setCellValue('D' . $counter, $gastoObj->proveedor->razon_social);
            $worksheet->setCellValue('G' . $counter, $gastoObj->descripcion);
            $worksheet->setCellValue('K' . $counter, Number::currency($gastoObj->importe, 'USD'));
            $worksheet->setCellValue('L' . $counter, $gastoObj->fecha->format('d-m-Y'));
            $worksheet->setCellValue('M' . $counter, $usuarioObj->nombre_completo);

            $worksheet->mergeCells('A' . $counter . ':C' . $counter);
            $worksheet->mergeCells('D' . $counter . ':F' . $counter);
            $worksheet->mergeCells('G' . $counter . ':J' . $counter);

            $worksheet->getStyle('A' . $counter)->getAlignment()->setHorizontal('center');
            $worksheet->getStyle('G' . $counter)->getAlignment()->setHorizontal('left');
            $worksheet->getStyle('K' . $counter)->getAlignment()->setHorizontal('right');
            $worksheet->getStyle('L' . $counter)->getAlignment()->setHorizontal('center');
            $worksheet->getStyle('M' . $counter)->getAlignment()->setHorizontal('left');
            $counter++;
        }

        $newName = 'reporte-proyecto-timesheet.xlsx';
        $writer  = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($pathname . $newName);

        return $this->response->withFile($pathname . $newName);
    }
}
