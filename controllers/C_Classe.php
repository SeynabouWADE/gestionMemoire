<?php
require_once("tools/Controller.php");
require_once("models/M_Classe.php");

class C_Classe extends Controller{
	protected $viewName = "classe";
	protected function setDefauls(){
		$this->model = new M_Classe();
        $this->dbTable = $this->model->getTable();
        $this->updateBackToList = get($this->model->tableInfo(), "update_back_to_tab_list"); //
        $this->errorInFormMsg = ob()->notice("Il y a eu des erreurs dans le formulaire ! &darr;");
        $this->createNotificationInfos = [
            "success" => [
                "Soumission avec succès. <a href=''>Veillez recevoir</a> nos sincères remerciements :)", 
                "success",
                "flash"
            ],
            "error" => [
                "Nous sommes désolé, il y a eu un problème inatendu lors de la soumission ): Veuillez recommencer svp.",
                "danger",
                "notification"
            ]
        ];
        $this->updateNotificationInfos = [
            "success" => [
                "Modification avec succès. <a href=''>Veillez recevoir</a> nos sincères remerciements :)", 
                "success",
                "flash"],
            "error" => [
                "Nous sommes désolé, il y a eu un problème inatendu lors de la soumission ): Veuillez recommencer svp.",
                "danger",
                "notification"
            ]
        ];
        $this->showColSmEqualColMd = true;
        $this->createColEqualColSm = true;
    }
    // public function setModel(){
    //     $this->model = new M_Table();
    // } 
    protected function home(){}
    public function adaptePostFirst(){}
    protected function create(){
        $tableInfo = $this->model->tableInfo();
      
        $this->userCan("c");
        $cardHeader = ob()->cardHeader($this->model->crudMsg("create", 2), "", 'primary', 'language');
        $viewInfo = [$this->viewName, "data"];
        $submitButton = [msgs["createSubmitButton"], "primary"];
        $cardFirstLine = right(
            a($this->model->crudMsg("create", 3), $this->route('read'), "button, secondary"));
        $this->createFrontEnd(
            "",
            toArrayOfArray(get($tableInfo, "other_tab")),
            4,
            "",
            toArray(get($tableInfo, "field_not_to_show_list_for_create")),
            true, 
            $submitButton,
            $cardHeader,
            $cardFirstLine, 
            $viewInfo,
            "",
            true,
            get($tableInfo, "ajax_validation")
        );
    }
    protected function read(){
        $this->userCan("r");
        $cardHeader = ob()->cardHeader($this->model->crudMsg("read", 2));
        $cardFirstLine = ""; //$this->model->crudMsg("read", 3);
        $viewInfo = [$this->viewName, "data"];
        $tableInfo = $this->model->tableInfo();

        $createRouteName = ternary2($this->route("create"), "", ",".get($tableInfo, "create_type"));
        $showRouteName = ternary2($this->route("show"), "", ",".get($tableInfo, "show_type"));
        $updateRouteName = ternary2($this->route("update"), "", ",".get($tableInfo, "update_type"));
        $deleteRouteName = ternary($this->route("delete"));
        $printRouteName = ternary2($this->route("print"), "", ",".get($tableInfo, "print_type"));
        $downloadRouteName = ternary($this->route("download"));
     
        //Gestion des droits
        $tmp = msgs['showActionButtonsWhenAccessDenied'] ? "$" : "";
        $createRouteName = $this->userCan("c", true) ? $createRouteName : ($createRouteName ? $tmp : "");
        $showRouteName = $this->userCan("s", true) ? $showRouteName : ($showRouteName ? $tmp : "");
        $updateRouteName = $this->userCan("u", true) ? $updateRouteName : ($updateRouteName ? $tmp : "");
        $deleteRouteName = $this->userCan("d", true) ? $deleteRouteName : ($deleteRouteName ? $tmp : "");
        $downloadRouteName = $this->userCan("do", true) ? $downloadRouteName : ($downloadRouteName ? $tmp : "");
        $printRouteName = $this->userCan("p", true) ? $printRouteName : ($printRouteName ? $tmp : "");
        $this->readFrontend(
            [
                $this->model->getTable(), //table name
                [], // whereAssoArr LimitTab
                "", // Orderby
                get($tableInfo, "number_of_lines_per_page"),// Define the number of lines per page otherwise it is the default (numberLinesPerPage) that will be taken
            ],
            [], // OderOrOnlyField
            toArray(get($tableInfo, "field_not_to_show_list_read")),
            toArrayOfArray(get($tableInfo, "other_tab")),
            toArray(get($tableInfo, "range_field_list")),
            get($tableInfo, "table_class"), 
            get($tableInfo, "line_number"), //"#", // or N°
            (bool) get($tableInfo, "search_zone"),
            get($tableInfo, "total_formul"),
            $showRouteName,
            $updateRouteName,
            $deleteRouteName,
            $downloadRouteName,
            $printRouteName,
            get($tableInfo, "stat_desc_col_list"),
            $cardHeader,
            $cardFirstLine,
            $viewInfo
        );
    }
    protected function show(){
        $this->userCan("s");
        $id = old("id");
        $cardHeader = ob()->cardHeader($this->model->crudMsg("show", 2), "", 'primary', 'language');
        $cardFirstLine = right(a($this->model->crudMsg("show", 3), $this->route('read'), "button, secondary"));;
        $viewInfo = [$this->viewName, "data"];
        $tableInfo = $this->model->tableInfo();
        $this->showFrontend(
            $id, 
            "",
            toArrayOfArray(get($tableInfo, "other_tab")),
            [],
            $cardHeader, 
            $cardFirstLine,
            $viewInfo,
            toArray(get($tableInfo, "field_not_to_show_list_for_show"))
        );
    }  
    protected function update($id = ""){
        $this->userCan("u");
        $tableInfo = $this->model->tableInfo();
        $cardHeader = ob()->cardHeader($this->model->crudMsg("update", 2), "", 'primary', 'language');
        $cardFirstLine = right(a($this->model->crudMsg("update", 3), $this->route('read'), "button, secondary"));
        $viewInfo = [$this->viewName, "data"];
        $submitButton = [msgs["updateSubmitButton"], "primary"];
        $this->updateFrontEnd(
            $id,
            "",
            toArrayOfArray(get($this->model->tableInfo(), "other_tab")),
            4,
            "",
            toArray(get($tableInfo, "field_not_to_show_list_for_create")), 
            true, 
            $submitButton, 
            $cardHeader,
            $cardFirstLine, 
            $viewInfo,
            true,
            get($tableInfo, "ajax_validation")
        );
    }
    protected function delete($id = "", $table = ""){
        $this->userCan("d");
        $id = post("id");
        echo $this->deleteBackend($id, $table);
    }
    
}