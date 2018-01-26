<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author kibunke
 */
require_once(PATH_VIEW . 'IndexView.php');
require_once(PATH_MODEL . 'CouchModel.php');
require_once(PATH_CONTROLLER . 'SessionController.php');

class IndexController {

    private static $instance;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 
     */
    private function paginador_process($pagina = 1) {
        $total = CouchModel::getInstance()->count();
        $limit = 9; //cantidad de paginas a mostrar
        $paginas = ceil($total / $limit); //cantidad de paginas en el sistema
        $offset = ($pagina - 1) * $limit;
        $couchinns = CouchModel::getInstance()->getCouchs(array(
            "limit" => $limit,
            "offset" => $offset
        ));
        return array(
            "paginas" => $paginas,
            "couchinns" => $couchinns
        );
    }

    /**
     * 
     */
    public function indexAction() {
        $session = SessionController::getInstance();
        $dataSession = array(
            "logueado" => NULL
        );
        if ($session->isLogginAction()) {
            $dataSession = $session->getData();
        }
        //Se obtiene de la BD las provincias
        $provincias = CouchModel::getInstance()->getProvincias();
        //Se obtiene de la BD los tipos de Couch Cargados
        $tipos = CouchModel::getInstance()->getTipos();

        $result = $this->paginador_process();
        $view = new IndexView();
        return $view->renderIndex(array(
                    "tipos" => $tipos,
                    "provincias" => $provincias,
                    "couchs" => $result['couchinns'],
                    "paginas" => $result['paginas'],
                    "pagActiva" => 1,
                    "session" => $dataSession
        ));
    }

    /**
     * 
     */
    public function ajax_paginadorAction() {
        $session = SessionController::getInstance();
        $dataSession = array(
            "logueado" => NULL
        );
        if ($session->isLogginAction()) {
            $dataSession = $session->getData();
        }
        $result = $this->paginador_process($_POST['pagina']);
        $view = new IndexView();
        $data['couchinns'] = $view->renderCouchinns(array(
            "couchs" => $result['couchinns'],
            "session" => $dataSession
        ));
        $data['paginador'] = $view->renderPaginador(array(
            "paginas" => $result['paginas'],
            "pagActiva" => $_POST['pagina']
        ));
        echo json_encode($data);
    }

    /**
     * Retorna couchs por un determinado tipo de couch
     * @param number $pagina
     * @param unknown $data
     */
    private function paginador_process_searchTipo($idTipoCouch = 1, $pagina = 1) {
        $total = CouchModel::getInstance()->countCouchsByTipo(array("idTipoCouch" => $idTipoCouch));
        $limit = 9; //cantidad de paginas a mostrar
        $paginas = ceil($total / $limit); //cantidad de paginas en el sistema
        $offset = ($pagina - 1) * $limit;
        $couchinns = CouchModel::getInstance()->searchCouchsByTipo(array(
            "idTipoCouch" => $idTipoCouch,
            "limit" => $limit,
            "offset" => $offset,
        ));
        return array(
            "paginas" => $paginas,
            "couchinns" => $couchinns
        );
    }

    private function paginador_process_searchProvincia($idProvincia = 1, $pagina = 1) {
        $total = CouchModel::getInstance()->countCouchsByProvincia(array("idProvincia" => $idProvincia));
        $limit = 9; //cantidad de paginas a mostrar
        $paginas = ceil($total / $limit); //cantidad de paginas en el sistema
        $offset = ($pagina - 1) * $limit;
        $couchinns = CouchModel::getInstance()->searchCouchsByProvincia(array(
            "idProvincia" => $idProvincia,
            "limit" => $limit,
            "offset" => $offset,
        ));
        return array(
            "paginas" => $paginas,
            "couchinns" => $couchinns
        );
    }

    private function paginador_process_searchDescripcion($descripcion, $pagina = 1) {
        $total = CouchModel::getInstance()->countCouchsByDescripcion(array("descripcion" => "%$descripcion%"));
        $limit = 9; //cantidad de paginas a mostrar
        $paginas = ceil($total / $limit); //cantidad de paginas en el sistema
        $offset = ($pagina - 1) * $limit;
        $couchinns = CouchModel::getInstance()->searchCouchsByDescripcion(array(
            "descripcion" => "%$descripcion%",
            "limit" => $limit,
            "offset" => $offset
        ));
        return array(
            "paginas" => $paginas,
            "couchinns" => $couchinns
        );
    }

    private function paginador_process_searchProvinciaCiudad($idProv, $idCdad, $pagina = 1) {
        $total = CouchModel::getInstance()->countCouchsByProvinciaCiudad(array("idProvincia" => $idProv, "idCiudad" => $idCdad));
        $limit = 9; //cantidad de paginas a mostrar
        $paginas = ceil($total / $limit); //cantidad de paginas en el sistema
        $offset = ($pagina - 1) * $limit;
        $couchinns = CouchModel::getInstance()->searchCouchsByProvinciaCiudad(array(
            "idProvincia" => $idProv,
            "idCiudad" => $idCdad,
            "limit" => $limit,
            "offset" => $offset
        ));
        return array(
            "paginas" => $paginas,
            "couchinns" => $couchinns
        );
    }

    //Probando si retornan los couchs
    private function paginador_process_searchAll($idTipo, $idProv, $idCdad, $descripcion, $pagina = 1) {
        $total = CouchModel::getInstance()->countCouchsByAll(array(
            "idTipoCouch" => $idTipo,
            "idProvincia" => $idProv,
            "idCiudad" => $idCdad,
            "descripcion" => "%$descripcion%"
        ));
        $limit = 9; //cantidad de paginas a mostrar
        $paginas = ceil($total / $limit); //cantidad de paginas en el sistema
        $offset = ($pagina - 1) * $limit;
        $parameters["limit"] = $limit;
        $parameters["offset"] = $offset;

        $couchinns = CouchModel::getInstance()->searchCouchsByAll(array(
            "idTipoCouch" => $idTipo,
            "idProvincia" => $idProv,
            "idCiudad" => $idCdad,
            "descripcion" => "%$descripcion%",
            "limit" => $limit,
            "offset" => $offset
        ));
        return array(
            "paginas" => $paginas,
            "couchinns" => $couchinns
        );
    }

    /**
     * Retorna los couchs filtrados por parametro
     */
    public function ajax_searchCouchsAction() {
        $session = SessionController::getInstance();
        $dataSession = array(
            "logueado" => NULL
        );
        if ($session->isLogginAction()) {
            $dataSession = $session->getData();
        }

        $pagina = 1;

        $idTipoCouch = $_POST['idTipoCouch'];
        $idProvincia = $_POST['idProvincia'];
        $idCiudad = $_POST['idCiudad'];
        $descripcion = $_POST['descripcion'];

        //campos vacios
        if ($idTipoCouch == '' && $idProvincia == '' && $idCiudad == '' && $descripcion == '') {
            $data['found'] = true;
            $data['msj'] = "No ha ingresado, ni seleccionado ningun filtro de búsqueda";
            $result['couchinns'] = false;
        }

        //Si es sólo por tipo
        if ($idTipoCouch != '' && $idProvincia == '' && $idCiudad == '' && $descripcion == '') {
            $result = $this->paginador_process_searchTipo($idTipoCouch, $pagina);
            $nombreTipo = CouchModel::getInstance()->getNombreTipoCouch(array("idTipoCouch" => $idTipoCouch));

            if ($result['couchinns']) {
                $data['found'] = false;
                $data['msj'] = "Resultados de la búsqueda por tipo de couch: <strong>" . $nombreTipo['nombre'] . "</strong>";
            } else {
                $data['found'] = true;
                $data['msj'] = "No hay resultados de búsqueda por tipo de couch: <strong>" . $nombreTipo['nombre'] . "</strong>";
            }
        }

        //Si es sólo por provincia
        if ($idTipoCouch == '' && $idProvincia != '' && $idCiudad == '' && $descripcion == '') {
            $result = $this->paginador_process_searchProvincia($idProvincia, $pagina);
            $nombreProv = CouchModel::getInstance()->getNombreProvincia(array("idProvincia" => $idProvincia));

            if ($result['couchinns']) {
                $data['found'] = false;
                $data['msj'] = "Resultados de la búsqueda por provincia: <strong>" . $nombreProv['nombre'] . "</strong>";
            } else {
                $data['found'] = true;
                $data['msj'] = "No hay resultados de búsqueda por provincia: <strong>" . $nombreProv['nombre'] . "</strong>";
            }
        }

        //LUGAR por provincia y ciudad
        if ($idTipoCouch == '' && $idProvincia != '' && $idCiudad != '' && $descripcion == '') {
            $result = $this->paginador_process_searchProvinciaCiudad($idProvincia, $idCiudad, $pagina);
            $nombreProv = CouchModel::getInstance()->getNombreProvincia(array("idProvincia" => $idProvincia));
            $nombreCdad = CouchModel::getInstance()->getNombreCiudad(array("idCiudad" => $idCiudad));

            if ($result['couchinns']) {
                $data['found'] = false;
                $data['msj'] = "Resultados de la búsqueda por lugar: <strong>" . $nombreCdad['nombre'] . " - " . $nombreProv['nombre'] . "</strong>";
            } else {
                $data['found'] = true;
                $data['msj'] = "No hay resultados de búsqueda por lugar: <strong>" . $nombreCdad['nombre'] . " - " . $nombreProv['nombre'] . "</strong>";
            }
        }

        //Si es sólo por Descripción
        if ($idTipoCouch == '' && $idProvincia == '' && $idCiudad == '' && $descripcion != '') {
            $result = $this->paginador_process_searchDescripcion($descripcion, $pagina);
            if ($result['couchinns']) {
                $data['found'] = false;
                $data['msj'] = "Resultados de la búsqueda con descripcion: <strong>$descripcion</strong";
            } else {
                $data['found'] = true;
                $data['msj'] = "No hay resultados de búsqueda con descripcion: <strong>$descripcion</strong>";
            }
        }

        //POR TODOS tipo, provincia, ciudad y descripcion
        if ($idTipoCouch != '' && $idProvincia != '' && $idCiudad != '' && $descripcion != '') {
            $result = $this->paginador_process_searchAll($idTipoCouch, $idProvincia, $idCiudad, $descripcion, $pagina);
            $nombreTipo = CouchModel::getInstance()->getNombreTipoCouch(array("idTipoCouch" => $idTipoCouch));
            $nombreProv = CouchModel::getInstance()->getNombreProvincia(array("idProvincia" => $idProvincia));
            $nombreCdad = CouchModel::getInstance()->getNombreCiudad(array("idCiudad" => $idCiudad));

            if ($result['couchinns']) {
                $data['found'] = false;
                $data['msj'] = "Resultado de búsqueda por tipo : <strong>" . $nombreTipo['nombre'] . "</strong> en lugar: <strong>" . $nombreCdad['nombre'] . " - " . $nombreProv['nombre'] . "</strong> y con descripcion: <strong>$descripcion</strong>";
            } else {
                $data['found'] = true;
                $data['msj'] = "No hay resultados de búsqueda por tipo de couch: <strong>" . $nombreTipo['nombre'] . "</strong> en lugar: <strong>" . $nombreCdad['nombre'] . " - " . $nombreProv['nombre'] . "</strong> y con descripcion: <strong>$descripcion</strong>";
            }
        }


        //     	//Si es por tipo y provincia
        //     	if ($idTipoCouch != "" && $idProvincia != "" && $idCiudad == "" && $descripcion == ""){
        //     	    $nombreTipo = CouchModel::getInstance()->getNombreTipoCouch(array("idTipoCouch" => $idTipoCouch));
        //     	    $result;
        //     	    if($result['couchinns']){
        //     	    	$data['error'] = false;
        //     	    	$data['msj'] = "<h4>Resultados de la búsqueda por tipo de couch: <strong>".$nombreTipo['nombre']."</strong> son:</h4>";
        //     	    }else {
        //     	    	$data['error'] = true;
        //     	    	$data['msj'] = "<h4>No hay resultados de búsqueda por tipo de couch: <strong>".$nombreTipo['nombre']."</strong></h4>";
        //     	    	return json_encode($data);
        //     	    }
        //     	}
        //     	//Si es por tipo, provincia y ciudad
        //     	if ($idTipoCouch != "" && $idProvincia != "" && $idCiudad != "" && $descripcion == ""){
        //     		$data['error'] = true;
        //     		$data['msj'] = "";
        //     	}
        //     	//Si es por tipo y descripcion
        //     	if ($idTipoCouch != "" && $idProvincia == "" && $idCiudad == "" && $descripcion != ""){
        //     		$data['error'] = true;
        //     		$data['msj'] = "";
        //     	}
        //     	//Si es por tipo, provincia y descripcion
        //     	if ($idTipoCouch != "" && $idProvincia != "" && $idCiudad == "" && $descripcion != ""){
        //     		$data['error'] = true;
        //     		$data['msj'] = "";
        //     	}
        //     	//Si es por provincia, ciudad y descripcion
        //     	if ($idTipoCouch == "" && $idProvincia != "" && $idCiudad != "" && $descripcion != ""){
        //     		$data['error'] = true;
        //     		$data['msj'] = "";
        //     	}
        if ($result['couchinns']) {
            $view = new IndexView();
            $data['couchinns'] = $view->renderCouchinns(array(
                "couchs" => $result['couchinns'],
                "session" => $dataSession
            ));
            $data['paginador'] = $view->renderPaginadorSearch(array(
                "paginas" => $result['paginas'],
                "pagActiva" => $pagina
            ));
        }
        echo json_encode($data);
    }

    public function ajax_paginadorSearchCouchsAction() {
        $session = SessionController::getInstance();
        $dataSession = array(
            "logueado" => NULL
        );
        if ($session->isLogginAction()) {
            $dataSession = $session->getData();
        }

        $pagina = $_POST['pagina'];

        $idTipoCouch = $_POST['idTipoCouch'];
        $idProvincia = $_POST['idProvincia'];
        $idCiudad = $_POST['idCiudad'];
        $descripcion = $_POST['descripcion'];

        //campos vacios
        if ($idTipoCouch == '' && $idProvincia == '' && $idCiudad == '' && $descripcion == '') {
            $data['found'] = true;
            $data['msj'] = "No ha ingresado, ni seleccionado ningun filtro de búsqueda";
            $result['couchinns'] = false;
        }

        //Si es sólo por tipo
        if ($idTipoCouch != '' && $idProvincia == '' && $idCiudad == '' && $descripcion == '') {
            $result = $this->paginador_process_searchTipo($idTipoCouch, $pagina);
            $nombreTipo = CouchModel::getInstance()->getNombreTipoCouch(array("idTipoCouch" => $idTipoCouch));

            if ($result['couchinns']) {
                $data['found'] = false;
                $data['msj'] = "Resultados de la búsqueda por tipo de couch: <strong>" . $nombreTipo['nombre'] . "</strong>";
            } else {
                $data['found'] = true;
                $data['msj'] = "No hay resultados de búsqueda por tipo de couch: <strong>" . $nombreTipo['nombre'] . "</strong>";
            }
        }

        //Si es sólo por provincia
        if ($idTipoCouch == '' && $idProvincia != '' && $idCiudad == '' && $descripcion == '') {
            $result = $this->paginador_process_searchProvincia($idProvincia, $pagina);
            $nombreProv = CouchModel::getInstance()->getNombreProvincia(array("idProvincia" => $idProvincia));

            if ($result['couchinns']) {
                $data['found'] = false;
                $data['msj'] = "Resultados de la búsqueda por provincia: <strong>" . $nombreProv['nombre'] . "</strong>";
            } else {
                $data['found'] = true;
                $data['msj'] = "No hay resultados de búsqueda por provincia: <strong>" . $nombreProv['nombre'] . "</strong>";
            }
        }

        //LUGAR por provincia y ciudad
        if ($idTipoCouch == '' && $idProvincia != '' && $idCiudad != '' && $descripcion == '') {
            $result = $this->paginador_process_searchProvinciaCiudad($idProvincia, $idCiudad, $pagina);
            $nombreProv = CouchModel::getInstance()->getNombreProvincia(array("idProvincia" => $idProvincia));
            $nombreCdad = CouchModel::getInstance()->getNombreCiudad(array("idCiudad" => $idCiudad));

            if ($result['couchinns']) {
                $data['found'] = false;
                $data['msj'] = "Resultados de la búsqueda por lugar: <strong>" . $nombreCdad['nombre'] . " - " . $nombreProv['nombre'] . "</strong>";
            } else {
                $data['found'] = true;
                $data['msj'] = "No hay resultados de búsqueda por lugar: <strong>" . $nombreCdad['nombre'] . " - " . $nombreProv['nombre'] . "</strong>";
            }
        }

        //Si es sólo por Descripción
        if ($idTipoCouch == '' && $idProvincia == '' && $idCiudad == '' && $descripcion != '') {
            $result = $this->paginador_process_searchDescripcion($descripcion, $pagina);
            if ($result['couchinns']) {
                $data['found'] = false;
                $data['msj'] = "Resultados de la búsqueda con descripcion: <strong>$descripcion</strong";
            } else {
                $data['found'] = true;
                $data['msj'] = "No hay resultados de búsqueda con descripcion: <strong>$descripcion</strong>";
            }
        }

        //POR TODOS tipo, provincia, ciudad y descripcion
        if ($idTipoCouch != '' && $idProvincia != '' && $idCiudad != '' && $descripcion != '') {
            $result = $this->paginador_process_searchAll($idTipoCouch, $idProvincia, $idCiudad, $descripcion, $pagina);
            $nombreTipo = CouchModel::getInstance()->getNombreTipoCouch(array("idTipoCouch" => $idTipoCouch));
            $nombreProv = CouchModel::getInstance()->getNombreProvincia(array("idProvincia" => $idProvincia));
            $nombreCdad = CouchModel::getInstance()->getNombreCiudad(array("idCiudad" => $idCiudad));

            if ($result['couchinns']) {
                $data['found'] = false;
                $data['msj'] = "Resultado de búsqueda por tipo : <strong>" . $nombreTipo['nombre'] . "</strong> en lugar: <strong>" . $nombreCdad['nombre'] . " - " . $nombreProv['nombre'] . "</strong> y con descripcion: <strong>$descripcion</strong>";
            } else {
                $data['found'] = true;
                $data['msj'] = "No hay resultados de búsqueda por tipo de couch: <strong>" . $nombreTipo['nombre'] . "</strong> en lugar: <strong>" . $nombreCdad['nombre'] . " - " . $nombreProv['nombre'] . "</strong> y con descripcion: <strong>$descripcion</strong>";
            }
        }

        // //Si es por tipo y provincia
        // if ($idTipoCouch != "" && $idProvincia != "" && $idCiudad == "" && $descripcion == ""){
        // $nombreTipo = CouchModel::getInstance()->getNombreTipoCouch(array("idTipoCouch" => $idTipoCouch));
        // $result;
        // if($result['couchinns']){
        // $data['error'] = false;
        // $data['msj'] = "<h4>Resultados de la búsqueda por tipo de couch: <strong>".$nombreTipo['nombre']."</strong> son:</h4>";
        // }else {
        // $data['error'] = true;
        // $data['msj'] = "<h4>No hay resultados de búsqueda por tipo de couch: <strong>".$nombreTipo['nombre']."</strong></h4>";
        // return json_encode($data);
        // }
        // }
        // //Si es por tipo, provincia y ciudad
        // if ($idTipoCouch != "" && $idProvincia != "" && $idCiudad != "" && $descripcion == ""){
        // $data['error'] = true;
        // $data['msj'] = "";
        // }
        // //Si es por tipo y descripcion
        // if ($idTipoCouch != "" && $idProvincia == "" && $idCiudad == "" && $descripcion != ""){
        // $data['error'] = true;
        // $data['msj'] = "";
        // }
        // //Si es por tipo, provincia y descripcion
        // if ($idTipoCouch != "" && $idProvincia != "" && $idCiudad == "" && $descripcion != ""){
        // $data['error'] = true;
        // $data['msj'] = "";
        // }
        // //Si es por provincia, ciudad y descripcion
        // if ($idTipoCouch == "" && $idProvincia != "" && $idCiudad != "" && $descripcion != ""){
        // $data['error'] = true;
        // $data['msj'] = "";
        // }
        if ($result ['couchinns']) {
            $view = new IndexView ();
            $data ['couchinns'] = $view->renderCouchinns(array(
                "couchs" => $result ['couchinns'],
                "session" => $dataSession
            ));
            $data ['paginador'] = $view->renderPaginadorSearch(array(
                "paginas" => $result ['paginas'],
                "pagActiva" => $pagina
            ));
        }
        echo json_encode($data);
    }

}
