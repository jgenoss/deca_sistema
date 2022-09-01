<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';
require_once '../modelo/devolucion.php';

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
            $button='<button id="dev" value="'.$row->id_salida.'" class="btn btn-warning"><i class="fas fa-undo"></i></button>';
            $A[] = array(
              ($row->id_salida)?$button:$button,
              $row->empresa,
              $row->referencia,
              $row->factura,
              $row->created_at,
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
    case 'getDevolucion':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($sl->getDevolucion());
          $A = array();
          foreach ($rtn as $row) {
            $button='<button id="view" value="'.$row->id_devolucion.'" class="btn btn-warning"><i class="fas fa-eye"></i></button>';
            $A[] = array(
              ($row->id_devolucion)?$button:$button,
              $row->empresa,
              $row->referencia,
              $row->factura,
              $row->created_at,
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
              '2' => $row->pNombre
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
            '7' => $_POST['id_salida'],
            '8' => $_POST['listp'],
            '9' => (!empty($_POST['file']))? $sl->ConvertFilePDF($_POST['file']):'',
          );
          try {
            $sl->setDevolucion($A,$_SESSION['START'][1]);
            setMsg("Info",'Devolucion Procesada con exito',"success");
          } catch (Exception $e) {
            setMsg("Error",$e->getMessage(),"error");
          }
        }
      }
      break;
    case 'valiDate':
      {
        if (isset($_POST)) {
          $id_serie = $_POST['sr'];
          $id_producto = $_POST['list']['id'];
          $rtn = Consult($db->sql("SELECT * FROM salida_detalle WHERE id_producto = '$id_producto'  AND id_serie ='$id_serie'"));
          if ($_POST['list']['cantidad'] > $rtn->cantidad) {
            setJson(array(
                'tittle' => 'ERROR',
                'message' => 'La cantidad es mayor a la existencia',
                'type' => 'error',
                'max' => $rtn->cantidad
              )
            );
          }else if ($_POST['list']['cantidad'] <= 0) {
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
          $rtn = Consult($db->sql("SELECT MAX(serie) as next  FROM devolucion"));
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
            'id_salida' => $rtn->id_salida,
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
    case 'getDevolucionId':
      {
        if (isset($_POST['id'])) {
          $rtn = Consult($db->sql("SELECT * FROM devolucion WHERE id_devolucion = ".$_POST['id']));
          $L = array();
          if ($rtn) {
            $rtn0 = AllConsult($sl->getDevolucionDetallada($rtn->serie));
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
          }
          $A = array(
            'total' => $total,
            'id_salida' => $rtn->id_salida,
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
              unlink($targetPath);
              $sheetCount = count($Reader->sheets());
              $A = array();
              for($i=0;$i<$sheetCount;$i++)
              {
                $Reader->ChangeSheet($i);
                foreach ($Reader as $key => $row)
                {
                  $A[] = array(
                    'COVA' => (isset($row[0]))? $row[0]:'',
                    'COAR' => (isset($row[1]))? $row[1]:'',
                    'REFERENCIA' => (isset($row[2]))? $row[2]:'',
                    'CANTIDAD' => (isset($row[3]))? $row[3]:''
                  );
                }
              }
              try {
                foreach ($A as $key) {
                  if (empty($key['COVA']) || empty($key['COAR'])|| empty($key['CANTIDAD'])) {
                    throw new Exception('formato mal estructurado');
                    break;
                  } elseif (!empty($key['COVA']) || !empty($key['COAR'])|| !empty($key['CANTIDAD'])) {

                    $COVA = $key['COVA'];
                    $COAR = $key['COAR'];
                    $CANTIDAD = $key['CANTIDAD'];

                    $rtn1 = Consult($db->sql("SELECT * FROM producto WHERE codigo_1='$COVA'"));
                    if ($rtn1) {
                      $id_producto = $rtn1->id_producto;
                      if (!$rtn1) {
                        throw new Exception('ESTE ARTICULO NO EXISTE'."<br/>".
                        "COAR: ".$key['COAR']."<br/>".
                        "COVA: ".$key['COVA']."<br/>".
                        "REFERENCIA: ".$key['REFERENCIA']."<br/>".
                        "CANTIDAD: ".$key['CANTIDAD']);
                        break;
                      }elseif ($rtn1) {
                        $L[] = array(
                          'id' => $rtn1->id_producto,
                          'codigo' => $rtn1->ean,
                          'nombre' => $rtn1->nombre,
                          'cantidad' => str_replace("-","",$key['CANTIDAD'])
                        );
                      }
                    }

                  }
                }
                setJson(array(
                  'tittle' => "Info",
                  'message' => "Cargue Masivo Procesado con exito",
                  'type' => "success",
                  'listp' => $L,
                ));
              } catch (Exception $e) {
                setMsg("Error",$e->getMessage(),"error");
              }
          }
        }
        break;
    default:
      die("NULL");
      break;
  }
