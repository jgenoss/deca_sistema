<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';
require_once '../modelo/salida.php';

  $sl = new salida();
  $db = new dbconnect();

  switch (@$_GET['op']) {
    case 'getClientes':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($sl->getCliente());
          $A = array();
          foreach ($rtn as $row) {
            $A[] = array(
              'id' => $row->id_cliente,
              'nombre' => $row->empresa
            );
          }
          setJson($A);
        }
      }
      break;
    case 'getSalidas':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($sl->getSalida());
          $A = array();
          foreach ($rtn as $row) {
            $url = "fpdf/rp_salida.php?id=";
            $button='<button id="view" value="'.$row->id_salida.'" class="btn btn-sm btn-warning"><i class="fas fa-eye"></i></button>
            <a class="btn btn-sm btn-info" target="_blank" href="'.$url.$row->id_salida.'" ><i class="fa-solid fa-print"></i></a>';
            $A[] = array(
              '0' => $button,
              '1' => $row->empresa,
              '2' => $row->referencia,
              '3' => $row->factura,
              '4' => $row->serie,
              '5' => $row->tpago,
              '6' => $row->created_at,
            );
          }
          setJson(array(
            "sEcho"=> 1,
            "iTotalRecords" => count($A),
            "iTotalDisplayRecords" => count($A),
            "data" => $A
          ));
        }
      }
      break;
    case 'getInventario':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($sl->getInventario($_GET['id']));
          $A = array();
          foreach ($rtn as $row) {
            $button='<button codigo="'.$row->ean.'" nombre="'.$row->pNombre.'" id="prod" value="'.$row->id_producto.'" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i></button>';
            $A[] = array(
              '0' => $button,
              '1' => '<span class="badge badge-primary">'.$row->codigo_1.'</span> <span class="badge badge-info">'.$row->codigo_2.'</span> <span class="badge badge-warning">'.$row->ean.'</span>',
              '2' => $row->pNombre,
              '3' => $row->cantidad,
              '4' => $row->cNombre,
            );
          }
          setJson(array(
            "sEcho"=> 1,
            "iTotalRecords" => count($A),
            "iTotalDisplayRecords" => count($A),
            "data" => $A
          ));
        }
      }
      break;
    case 'getProductId':
      {
        if (isset($_POST)) {
          $rtn = Consult($sl->getProductId($_POST['id']));
          $A = array(
            'id' => $rtn->id_producto,
            'producto' => $rtn->pNombre,
            'codigo' => $rtn->codigo
          );
          setJson($A);
        }
      }
      break;
    case 'regSalida':
      {

        if (isset($_POST)) {
          $A = array(
            '0' => $_POST['id_cliente'],
            '1' => $_POST['referencia'],
            '2' => $_POST['factura'],
            '3' => $_POST['fecha'],
            '4' => $_POST['serie'],
            '5' => $_POST['observacion'],
            '6' => $_POST['tpago'],
            '7' => $_POST['file'],
            '8' => $_POST['listp']
          );
          try {
            $A[7] = $sl->ConvertFilePDF($A[7]);
            $sl->setSalida($A);
            setMsg("Info",'Salida Procesada con exito',"success");
          } catch (Exception $e) {
            setMsg("Error",$e->getMessage(),"error");
          }
        }
      }
      break;
    case 'valiDate':
      {
        if (isset($_POST)) {
          $rtn = Consult($db->sql("SELECT * FROM inventario WHERE id_producto =".$_POST['id']));
          if ($_POST['cantidad'] > $rtn->cantidad) {
            setJson(array(
                'tittle' => 'ERROR',
                'message' => 'La cantidad es mayor a la existencia',
                'type' => 'error',
                'max' => $rtn->cantidad
              )
            );
          }else if ($_POST['cantidad'] <= 0) {
            setJson(array(
                'tittle' => 'ERROR',
                'message' => 'Por favor introdusca una cantidad valida',
                'type' => 'info',
                'max' => 0
              )
            );
          }
        }
      }
      break;
    case 'getConsecutivo':
      {
        if (isset($_GET)) {
          $rtn = Consult($db->sql("SELECT MAX(serie) as next  FROM salida"));
          if ($rtn) {
            setJson(array('next' => $rtn->next+1));
          }else {
            setJson(array('next' => 1));
          }
        }
      }
      break;
    case 'getSalidaId':
      {
        if (isset($_POST['id'])) {
          $rtn = Consult($sl->getSalidaId($_POST['id']));
          $L = array();
          $rtn0 = AllConsult($sl->getsalidaDetallada($rtn->serie));
          $total=0;
          foreach ($rtn0 as $key) {
            $total += $key->cantidad;
            $L[] = array(
              'id' => $key->id_producto,
              'codigo' => $key->ean,
              'nombre' => $key->nombre,
              'cantidad' => $key->cantidad
            );
          }
          $A = array(
            'total' => $total,
            'id_cliente' => $rtn->id_cliente,
            'referencia' => $rtn->referencia,
            'factura' => $rtn->factura,
            'fecha' => $rtn->fecha_de_comprobante,
            'file' => $rtn->archivo,
            'serie' => $rtn->serie,
            'observacion' => $rtn->observacion,
            'tpago' => $rtn->tpago,
            'listp' => $L
          );
          setJson($A);
        }
      }
      break;
      case 'uploadFile':
          {
            require_once('../spreadSheetReader/php-excel-reader/excel_reader2.php');
            require_once('../spreadSheetReader/SpreadsheetReader.php');
            if (isset($_REQUEST)) {
              $file = $_FILES['file'];
              $file_name = file_get_contents($file['tmp_name']);
              // $file2 = explode("\n", $file1);
              // $file3 = array_filter($file2);
                $targetPath = '../upload_files/'.$file['name'];
                move_uploaded_file($file['tmp_name'], $targetPath);
                $Reader = new SpreadsheetReader($targetPath);
                unlink('../upload_files/'.$targetPath);
                $sheetCount = count($Reader->sheets());
                $A[] = array();
                $B[] = array();
                for($i=0;$i<$sheetCount;$i++)
                {
                  $Reader->ChangeSheet($i);
                  foreach ($Reader as $key => $row)
                  {
                    $A[$key] = array(
                      'codigo' => (isset($row[0]))? $row[0]:'',
                      'nombre' => (isset($row[1]))? str_limit($row[1],55,'...'):'',
                      'cantidad' => (isset($row[2]))? $row[2]:''
                    );
                    $cantidad = 0;
                    if (array_key_exists($A[$key]['codigo'],$A)) {
                      $B[] = array(
                        'codigo' => $row['codigo'],
                        'nombre' => $row['nombre'],
                        'cantidad' => $cantidad += $row['cantidad']
                      );
                    }else {
                      $B[] = array(
                        'codigo' => $row['codigo'],
                        'nombre' => $row['nombre'],
                        'cantidad' => $row['cantidad']
                      );
                    }
                  }
                }
                setJson($A);
                // try {
                //   foreach ($A as $key) {
                //     if (empty($key['ean']) || empty($key['nombre'])||empty($key['cantidad'])) {
                //       throw new Exception('formato mal estructurado');
                //       break;
                //     } elseif (!empty($key['ean']) || !empty($key['nombre']) || !empty($key['cantidad'])) {
                //       $L[] = array(
                //         'codigo' => $key['ean'],
                //         'nombre' => $key['nombre'],
                //         'cantidad' => $key['cantidad']
                //       );
                //     }
                //   }
                //   // setJson($L);
                //   // setMsg("Info",'Cargue Procesado con exito',"success");
                // } catch (Exception $e) {
                //   setMsg("Error",$e->getMessage(),"error");
                // }
            }
          }
        break;
    default:
      die("NULL");
      break;
  }
