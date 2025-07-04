<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';
require_once '../modelo/salida.php';

$sl = new salida();
$db = new dbconnect();

switch (@$_GET['op']) {
  case 'getClientes': {
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
  case 'getSalidas': {
      if (isset($_GET)) {
        $rtn = AllConsult($sl->getSalida());
        $A = array();
        foreach ($rtn as $row) {
          $url = "fpdf/rp_salida.php?id=";
          $button = '<div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-cog"></i></button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <button id="view" value="' . $row->id_salida . '" class="dropdown-item"><i class="fas fa-eye"></i> Ver</button>
                <button id="edit" value="' . $row->id_salida . '" class="dropdown-item"><i class="fas fa-edit"></i> Editar</button>
                <a target="_blank" href="' . $url . $row->id_salida . '" class="dropdown-item"><i class="fa-solid fa-print"></i> Imprimir</a>
            </div>
          </div>';

          $A[] = array(
            $button,
            $row->empresa,
            ($row->devolucion == 1) ? '<span class="badge badge-warning">' . $row->referencia . ' ' . $sl->getDevType($row->id_salida)->tdevolucion . '</span>' : $row->referencia,
            $row->factura,
            $row->id_salida,
            $sl->getUndCaj($row->id_salida)['cantidad'],
            $sl->getUndCaj($row->id_salida)['cajas'],
            $row->fecha_de_comprobante,
            $row->created_at
          );
        }
        setJson(
          array(
            "sEcho" => 1,
            "iTotalRecords" => count($A),
            "iTotalDisplayRecords" => count($A),
            "data" => $A
          )
        );
      }
    }
    break;
  case 'getListDate': {
      if (isset($_GET)) {
        $rtn = AllConsult($sl->getListDate(array($_GET['f_start'], $_GET['f_end'])));
        $A = array();
        foreach ($rtn as $row) {
          $url = "fpdf/rp_salida.php?id=";
          $button = '<button id="view" value="' . $row->id_salida . '" class="btn btn-sm btn-warning"><i class="fas fa-eye"></i></button>
            <a class="btn btn-sm btn-info" target="_blank" href="' . $url . $row->id_salida . '" ><i class="fa-solid fa-print"></i></a>';
          $A[] = array(
            $button,
            $row->empresa,
            ($row->devolucion == 1) ? '<span class="badge badge-warning">' . $row->referencia . '</span>' : $row->referencia,
            $row->factura,
            $row->id_salida,
            $sl->getUndCaj($row->id_salida)['cantidad'],
            $sl->getUndCaj($row->id_salida)['cajas'],
            $row->fecha_de_comprobante,
            $row->created_at
          );
        }
        setJson(
          array(
            "sEcho" => 1,
            "iTotalRecords" => count($A),
            "iTotalDisplayRecords" => count($A),
            "data" => $A
          )
        );
      }
    }
    break;
  case 'getInventario': {
      if (isset($_GET)) {
        $rtn = AllConsult($sl->getInventario($_GET['id']));
        $A = array();
        foreach ($rtn as $row) {
          $button = '<button codigo="' . $row->ean . '" nombre="' . $row->pNombre . '" id="prod" value="' . $row->id_producto . '" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i></button>';
          $A[] = [
            '0' => $button,
            '1' => $row->codigo_1 . ' ' . $row->codigo_2 . ' ' . $row->ean,
            '2' => $row->pNombre,
            '3' => $row->cantidad,
            '4' => $row->bNombre
          ];
        }
        setJson(
          array(
            "sEcho" => 1,
            "iTotalRecords" => count($A),
            "iTotalDisplayRecords" => count($A),
            "data" => $A
          )
        );
      }
    }
    break;
  case 'getProductId': {
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
  case 'regSalida': {

      if (isset($_POST)) {
        $A = array(
          '0' => $_POST['id_cliente'],
          '1' => $_POST['referencia'],
          '2' => $_POST['factura'],
          '3' => $_POST['fecha'],
          '4' => $_POST['serie'],
          '5' => $_POST['observacion'],
          '6' => $_POST['direccion'],
          '7' => $_POST['tpago'],
          '8' => $_POST['file'],
          '9' => $_POST['listp']
        );
        try {
          $rtn = Consult($db->sql("SELECT * FROM salida WHERE factura = '$A[2]'"));
          if ($rtn) {
            throw new Exception('esta salida ya fue procesada');
          } else {
            $A[8] = $sl->ConvertFilePDF($A[8]);
            $sl->setSalida($A);
          }
          setMsg("Info", 'Salida Procesada con exito', "success");
        } catch (Exception $e) {
          setMsg("Error", $e->getMessage(), "error");
        }
      }
    }
    break;
  case 'editSalida': {
      if (isset($_POST)) {
        $val = array(
          '0' => $_POST['id_cliente'],
          '1' => $_POST['referencia'],
          '2' => $_POST['factura'],
          '3' => $_POST['fecha'],
          '4' => $_POST['serie'],
          '5' => $_POST['observacion'],
          '6' => $_POST['direccion'],
          '7' => $_POST['tpago'],
          '8' => $_POST['file'],
          '9' => $_POST['listp'],
          '10' => $_POST['id_salida']
        );

        try {
          $val[8] = $sl->ConvertFilePDF($val[8]);

          $query = $db->sql("UPDATE salida SET id_cliente = '$val[0]', referencia = '$val[1]', fecha_de_comprobante = '$val[3]', serie = '$val[4]', observacion = '$val[5]', direccion = '$val[6]', tpago = '$val[7]', archivo = '$val[8]', factura = '$val[2]' WHERE id_salida = '$val[10]'");
          $last_id = $db->lastInsertId();
          if ($query) {

            $detallesAntiguos = $db->AllConsult($db->sql("SELECT id_producto, cantidad FROM salida_detalle WHERE id_salida='$val[10]'"));

            // Elimina los registros de salida_detalle para la salida existente
            $db->sql("DELETE FROM salida_detalle WHERE id_salida = '$val[10]'");

            // Elimina los registros de movimientos para la salida existente
            $db->sql("DELETE FROM movimientos WHERE referencia = '$val[1]' AND factura = '$val[2]'");

            // Restablece el inventario para los productos de la salida existente
            foreach ($detallesAntiguos as $detalle) {
              $id = $detalle->id_producto;
              $cantidad = $detalle->cantidad;
              $db->sql("UPDATE inventario SET cantidad=cantidad+'$cantidad' WHERE id_producto=" . $id);
            }
            foreach ($val[9] as $detalle) {
              $id = $detalle['id'];
              $cantidad = $detalle['cantidad'];

              $query = $db->sql("INSERT INTO salida_detalle (id_salida, id_serie, id_producto, cantidad) VALUES ('$val[10]', '$val[4]', '$id', '$cantidad')");

              $db->sql("INSERT INTO movimientos (fecha, tipo, cantidad, producto_id, referencia, factura, fv, fecha_vencimiento) VALUES ('$val[3]', 'salida', '$cantidad', '$id', '$val[1]', '$val[2]', '0', '0000-00-00')");

              $query = $db->sql("UPDATE inventario SET cantidad = cantidad - '$cantidad' WHERE id_producto = $id AND status = 1");
              $queryi = $db->Consult($db->sql("SELECT * FROM inventario WHERE id_producto = " . $id));

              if ($queryi) {
                $inv_cantidad = $queryi->cantidad;
                $db->sql("INSERT INTO inventario_fecha (id_salida, id_producto, cantidad, fecha) VALUES ('$last_id', '$id', '$inv_cantidad', '$val[3]')");
              }
            }
          }

          setMsg("Info", 'Salida actualizada con éxito.', "success");
        } catch (Exception $e) {
          setMsg("Error", $e->getMessage(), "error");
        }
      }
    }
    break;
  case 'valiDate': {
      if (isset($_POST)) {
        $rtn = Consult($db->sql("SELECT * FROM inventario WHERE id_producto =" . $_POST['id']));
        if ($_POST['cantidad'] > $rtn->cantidad) {
          setJson(
            array(
              'tittle' => 'ERROR',
              'message' => 'La cantidad es mayor a la existencia',
              'type' => 'error',
              'max' => $rtn->cantidad
            )
          );
        } else if ($_POST['cantidad'] <= 0) {
          setJson(
            array(
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
  case 'getConsecutivo': {
      if (isset($_GET)) {
        $rtn = Consult($db->sql("SELECT MAX(serie) as next  FROM salida"));
        if ($rtn) {
          setJson(array('next' => $rtn->next + 1));
        } else {
          setJson(array('next' => 1));
        }
      }
    }
    break;
  case 'getSalidaId': {
      if (isset($_POST['id'])) {
        $rtn = Consult($sl->getSalidaId($_POST['id']));
        $L = array();
        $rtn0 = AllConsult($sl->getsalidaDetallada($rtn->id_salida));
        $total = 0;
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
          'serie' => $rtn->id_salida,
          'observacion' => $rtn->observacion,
          'direccion' => $rtn->direccion,
          'tpago' => $rtn->tpago,
          'listp' => $L
        );
        setJson($A);
      }
    }
    break;
  case 'uploadFile': {
      require_once('../spreadSheetReader/php-excel-reader/excel_reader2.php');
      require_once('../spreadSheetReader/SpreadsheetReader.php');
      if (isset($_REQUEST)) {
        $file = $_FILES['file'];
        $file_name = file_get_contents($file['tmp_name']);
        // $file2 = explode("\n", $file1);
        // $file3 = array_filter($file2);
        $targetPath = '../upload_files/' . $file['name'];
        move_uploaded_file($file['tmp_name'], $targetPath);
        $Reader = new SpreadsheetReader($targetPath);
        unlink($targetPath);
        $sheetCount = count($Reader->sheets());
        $A = array();
        for ($i = 0; $i < $sheetCount; $i++) {
          $Reader->ChangeSheet($i);
          foreach ($Reader as $key => $row) {
            $A[] = array(
              'COAR' => (isset($row[0])) ? $row[0] : '',
              'COVA' => (isset($row[1])) ? $row[1] : '',
              'REFERENCIA' => (isset($row[2])) ? $row[2] : '',
              'TIPO' => (isset($row[3])) ? $row[3] : '',
              'EAN' => (isset($row[4])) ? $row[4] : '',
              'CANTIDAD' => (isset($row[5])) ? $row[5] : ''
            );
          }
        }
        try {
          foreach ($A as $key) {
            if (empty($key['COVA']) || empty($key['COAR']) || empty($key['CANTIDAD'])) {
              throw new Exception('formato mal estructurado' . "<br/>" .
                "COAR:" . $key['COAR'] . "<br/>" .
                "COVA:" . $key['COVA'] . "<br/>" .
                "REFERENCIA:" . $key['REFERENCIA'] . "<br/>" .
                "TIPO:" . $key['TIPO'] . "<br/>" .
                "EAN:" . $key['EAN'] . "<br/>" .
                "CANTIDAD:" . $key['CANTIDAD']);
              break;
            } elseif (!empty($key['COVA']) || !empty($key['COAR']) || !empty($key['EAN']) || !empty($key['CANTIDAD'])) {

              $COVA = $key['COVA'];
              $COAR = $key['COAR'];
              $EAN = $key['EAN'];
              $TIPO = $key['TIPO'];
              $CANTIDAD = $key['CANTIDAD'];

              $rtn1 = Consult($db->sql("SELECT * FROM producto WHERE codigo_1='$COVA' AND status = 1"));
              if (!$rtn1) {
                throw new Exception('ESTE ARTICULO NO EXISTE' . "<br/>" .
                  "COAR: " . $key['COAR'] . "<br/>" .
                  "COVA: " . $key['COVA'] . "<br/>" .
                  "REFERENCIA: " . $key['REFERENCIA'] . "<br/>" .
                  "EAN: " . $key['EAN'] . "<br/>" .
                  "CANTIDAD: " . $key['CANTIDAD']);
                break;
              } elseif ($rtn1) {
                $idp = $rtn1->id_producto;
                $rtn2 = Consult($db->sql("SELECT * FROM inventario WHERE id_producto='$idp' AND status = 1"));
                if (!$rtn2) {
                  throw new Exception('NO EXISTE EN INVENTARIO' . "<br/>" .
                    "COAR: " . $key['COAR'] . "<br/>" .
                    "COVA: " . $key['COVA'] . "<br/>" .
                    "REFERENCIA: " . $key['REFERENCIA'] . "<br/>" .
                    "EAN: " . $key['EAN'] . "<br/>" .
                    "CANTIDAD: " . $key['CANTIDAD']);
                  break;
                } elseif ($rtn1) {
                  if ($rtn2) {
                    if ($rtn2->cantidad >= $key['CANTIDAD']) {
                      if (($key['TIPO'] == 'Display') && ($rtn1->tipo == 'Display')) {
                        $cant = $rtn1->tipo_val * $key['CANTIDAD'];
                        $L[] = array(
                          'id' => $rtn1->id_producto,
                          'codigo' => $rtn1->ean,
                          'nombre' => $rtn1->nombre,
                          'tipo' => 'Display',
                          'cantidad' => $cant
                        );
                      } elseif ($key['TIPO'] == 'Caja') {
                        $L[] = array(
                          'id' => $rtn1->id_producto,
                          'codigo' => $rtn1->ean,
                          'nombre' => $rtn1->nombre,
                          'tipo' => 'Caja',
                          'cantidad' => $rtn1->umb * $key['CANTIDAD']
                        );
                      } elseif ($key['TIPO'] == 'Unidad') {
                        $L[] = array(
                          'id' => $rtn1->id_producto,
                          'codigo' => $rtn1->ean,
                          'nombre' => $rtn1->nombre,
                          'tipo' => 'Unidad',
                          'cantidad' => $key['CANTIDAD']
                        );
                      } else {
                        $L[] = array(
                          'id' => $rtn1->id_producto,
                          'codigo' => $rtn1->ean,
                          'nombre' => $rtn1->nombre,
                          'tipo' => 'Unidad',
                          'cantidad' => $key['CANTIDAD']
                        );
                      }

                    } elseif ($key['CANTIDAD'] > $rtn2->cantidad) {
                      throw new Exception('CANTIDAD MAYOR A EXISTENCIA' . "<br/>" .
                        "COAR: " . $key['COAR'] . "<br/>" .
                        "COVA: " . $key['COVA'] . "<br/>" .
                        "REFERENCIA: " . $key['REFERENCIA'] . "<br/>" .
                        "EAN: " . $key['EAN'] . "<br/>" .
                        "CANTIDAD: " . $key['CANTIDAD']);
                      break;
                    }
                  }
                }
              }
            }
          }
          setJson(
            array(
              'tittle' => "Info",
              'message' => "Cargue Masivo Procesado con exito",
              'type' => "success",
              'listp' => $L,
            )
          );
        } catch (Exception $e) {
          setMsg("Error", $e->getMessage(), "error");
        }
      }
    }
    break;
  default:
    die("NULL");
    break;
}