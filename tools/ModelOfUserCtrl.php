

    protected function logIn(){
        $tableInfo = $this->model->tableInfo();
      
        //$this->userCan("c");
        $cardHeader = ob()->cardHeader($this->model->crudMsg("logIn", 2), "", 'primary', 'language');
        $viewInfo = [$this->viewName, "data"];
        $submitButton = [msgs["createSubmitButton"], "primary"];
        $cardFirstLine = right(
            a($this->model->crudMsg("create", 3), $this->route('read'), "button, secondary"));
        $this->logInFrontend(
            "",
            toArrayOfArray(get($tableInfo, "other_tab")),
            4,
            "",
            array_merge(["first_name", "last_name", "user_type"], toArray(get($tableInfo, "field_not_to_show_list"))),
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
    protected function registration(){
            $tableInfo = $this->model->tableInfo();
      
        //$this->userCan("c");
        $cardHeader = ob()->cardHeader($this->model->crudMsg("registration", 2), "", 'primary', 'language');
        $viewInfo = [$this->viewName, "data"];
        $submitButton = [msgs["createSubmitButton"], "primary"];
        $cardFirstLine = right(
            a($this->model->crudMsg("create", 3), $this->route('read'), "button, secondary"));
        $this->createFrontEnd(
            "",
            toArrayOfArray(get($tableInfo, "other_tab")),
            4,
            "",
            array_merge(["user_type"], toArray(get($tableInfo, "field_not_to_show_list"))),
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