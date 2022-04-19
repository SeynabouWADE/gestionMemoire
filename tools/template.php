<!--
=========================================================
Material Dashboard - v2.1.2
=========================================================

Product Page: https://www.creative-tim.com/product/material-dashboard
Copyright 2020 Creative Tim (https://www.creative-tim.com)
Coded by Creative Tim

=========================================================
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="tools/assets/img/apple-icon.png">
  <!-- <link rel="icon" type="image/png" href="tools/assets/img/favicon.png"> -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title><?= $title ?></title>

  <script src="tools/assets/js/core/jquery.min.js"></script>
  <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script> -->
  <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="tools/assets/css/ionRangeSliderColor.css" rel="stylesheet" />
  <link href="tools/assets/css/material-dashboard.css" rel="stylesheet" />
  <link href="tools/assets/css/typeaheader.css" rel="stylesheet" />
  <!-- <link href="https://demos.creative-tim.com/test/material-dashboard-pro/assets/css/material-dashboard.min.css?v=2.0.3" rel="stylesheet" /> -->
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="tools/assets/demo/demo.css" rel="stylesheet" />
  
  <script src="tools/assets/js/lang/<?= lang ?>/messages.js"></script>
  <script>
    (function($){
      $('.addPanierJs').click(function(event){
        event.preventDefault();
        //pour récupérer l'url à appeler (se trouvant là où on a cliqué
        $.get($(this).attr('href'),{},function(data){ // data c'est les informations qu'on récuépère en retour apr ajax
          if( ! data.error){
            // if(data.totalPartiel != 0){sep = ' |'; unite = ' F CFA';}
            // else{sep = ''; unite = '';}
            sep = "|";
            unite = "";
            // $('#totalPartiel').empty().append(data.totalPartiel + sep);
            // $('#totalArticle').empty().append(data.totalArticle + sep);
            // $('#totalQuantity').empty().append(data.totalQuantity + sep);
            $('#totalArtQuan').empty().append(data.totalQuantity + sep + data.totalArticle);
            $('#total').empty().append(new Intl.NumberFormat().format(data.total + unite));
          }
        },'json');   //le format en dernier paramètre
        return false;
      });	
    })(jQuery);


    //TODO doc
    function ajax(data, urlOrRoute, successFunction, type="post", csrf = "") {
      data.tokencsrf = csrf;
      data.multipleSubmitAccepted = "<?= multipleSubmitAccepted ?>";
      data.isNotBackLink = 1;
      $.ajax({
        type: type,
        url: urlOrRoute,
        data: data,
        //cache: false,
        success: function(response) { //TODO sécurité : sur Function ... 
          Function('"use strict";return ('+successFunction+"('"+escape(response)+"')" +')')();
          //eval(successFunction + "('"+escape(response)+"')"); // TODO penser à échapper les appostrophes
          //eval(successFunction + "('"+escape(response)+"')");
        },
        error : function(resultat, statut, erreur){
          console.log(resultat.responseText);
					swalError("<?=msg('mayBeDeconnected')?>");
        }/* ,
        complete :function(resultat, statut){
          alert(statut);
          console.log(resultat.responseText);
        } */
      });
    }
    function pamTestfct(ajaxResponse){
      alert(unescape(ajaxResponse));
    }
    function ajaxDependance(data, type="post", csrf = "") {
      data.tokencsrf = csrf;
      data.multipleSubmitAccepted = "<?= multipleSubmitAccepted ?>";
      data.isNotBackLink = 1;
      $.ajax({
        type: type,
        url: "ajaxDependance",
        data: data,
        //cache: false,
        success: function(response) {
          $("#block0"+data.targetField).html(response);
          $('#'+data.targetField).selectpicker();
        },
        error : function(resultat, statut, erreur){
          console.log(resultat.responseText);
					swalError("<?=msg('mayBeDeconnected')?>");
        }/* ,
        complete :function(resultat, statut){
          alert(statut);
          console.log(resultat.responseText);
        } */
      });
    }
    function ajaxServiceFromDepart(ajaxResponse){
      $("#block0service").html(unescape(ajaxResponse));
      $('#service').selectpicker();
      $('#service').click();
      //$('select').selectpicker();
    } 
    function ajaxServiceFromPrelevement(ajaxResponse){
      $("#block0prelevement").html(unescape(ajaxResponse));
      $('#prelevement').selectpicker();
      $('#prelevement').click();
    }
    function addToCreate(argThis, id){
      var val = $("#"+id).val().trim();
      if(val.length){
        argThis.href = (argThis.href).split("&")[0];
        argThis.href += "&arg="+val;
      }
    }
    function setVal(value, idOrClass, tooltipTypeahead = ""){// idOrClass add # if id and . if classe
      $(idOrClass).val(value);
      if(tooltipTypeahead){
        idOrClass = idOrClass.replace("#tt", "ttQ");
        tooltip(idOrClass, tooltipTypeahead);
      }
    }
    //Le mettre en php <script src="tools/assets/js/core/popper.min.js">< /script>
    /**
     * si on utlise cette fonction en js aulieu de passer par la même fonction php alors il faut s'assurer que l'importation de ce qui suit est bien fait : <script src="tools/assets/js/core/popper.min.js">< /script>.
     * Si vous importer manuellement dans le template alors il faut mettre à false la constante tooltipImportOptional dans config.php
     */
    function tooltip(id, title, placement="top", option="enable",optionInStart = "show"){ // option : show|hide|toggle|dispose|enable|disable  see https://getbootstrap.com/docs/4.0/components/tooltips/
      id = "#"+id;
      $(id).tooltip('dispose');
      $(id).attr("data-toggle", "tooltip");
      $(id).attr("data-placement", placement);
      $(id).attr("title", title);
      if(optionInStart)
        $(id).tooltip(optionInStart);
      $(id).tooltip(option);
    }
    function tooltipOption(id, option="dispose", optionInStart = "show"){ // option : show|hide|toggle|dispose|enable|disable
      if(optionInStart)
        $(id).tooltip(optionInStart);
      $("#".id).tooltip(option);
    }
    function tooltipDispose(id){ 
      $(id).tooltip("dispose");
    }
    function setValIfEmpty(value, idOrClass, idOrClassWitchIsEmpty){// idOrClass add # if id and . if classe
      if($(idOrClassWitchIsEmpty).val() == ""){
        $(idOrClass).val(value);
        $(idOrClassWitchIsEmpty).tooltip('dispose');
      
      }
    }

    //function ajaxFormValidator(data0, csrf = "", isFile = false, urlOrRoute = "< ?=controllerName()?>/ajaxFormValidato", type="post"){
    function generAjaxFormValidator(number, e = null){
      ajaxFormValidator("", "", "", "", "post", number, e);
    }
    function see(data){
      console.log(data);
    }
    function optionIndexFromText(id, text){
      $("#"+id+" option:contains('"+text+"')").index();
    }
    function optionIndexFromValue(value){
      $("#"+id+" option[value='"+value+"']").index();
    }
    function jsonToObject(str){
      try {
        var o = JSON.parse(str);
        return o;
      } catch (e) {
        return false;
      }
    }
    //function ajaxFormValidator(data0, csrf = "", isFile = false, urlOrRoute = "< ?=controllerName()?>/ajaxFormValidato", type="post"){
    function ajaxFormValidator(id = "", urlOrRoute = "", rule = "", eventType = "", type="", number = "", e = null){
      var elt;
      if(id)  elt = document.getElementById(id);
      else    elt = document.activeElement;
      id = elt.id; // ? elt.id : elt.name;
      if(id == "" || id == null || typeof id === undefined){ // pour les select
        id = elt.getAttribute("data-id");
        if(id) elt = document.getElementById(id);
      }
      if(id && elt.name){
        //eventType = (eventType == "") ? "blur" : eventType;
        var validEventType = elt.getAttribute("valideventtype")
        if( eventType == "" )
          eventType = validEventType;
        
        if( eventType == "none" ) //|| eventType == "" || eventType == null)
          return;

        eltType = elt.type.split("-")[0].toLowerCase();
        if(eltType == "select" || eltType == "radio" || eltType == "checkbox")
          eventType = "change";

        if(e.which == 13 && eventType != "change")
        return
          
        
        // if(id != id0)
        //   eventType = "click change blur";
        $("#"+id).on(eventType, "", function(){
          if(type == "")
            type = $("#"+id).parents("form").attr("method");
          type = (type.toLowerCase() === "get") ? "get" : "post";
          urlOrRoute = urlOrRoute ? urlOrRoute : "<?=controllerName()?>/ajaxFormValidator";
          var data = new Object();
          data.fieldName = elt.name;
          data[elt.name] = elt.value;
          data.id = id;
          data.rule = rule;
          data.multipleSubmitAccepted = "<?= multipleSubmitAccepted ?>";
          if(type == "post")
            data.tokencsrf = "<?=csrfToken()?>";
                  
          //type, size, color
          var spinner = $("#ajax-spinner-style"+number).text().split(",");
          var spinnerType = spinner[0];
          var spinnerSize = spinner[1];
          var spinnerColor = spinner[2];

          var feedbackIconId = "#feedbackIcon"+id;
          var block1Id = "#block1"+id;

          $(feedbackIconId).remove();

          if(spinnerType != "border"){
            var $spinner = $('<?= spinner("grow")?>').attr('id', 'feedbackIcon'+id);
            spinnerType = "grow";
          }else {
            var $spinner = $('<?= spinner("border")?>').attr('id', 'feedbackIcon'+id);
            spinnerType = "border";
          }
          $(block1Id).append($spinner);
          
          if(spinnerSize == "sm"){
            $(feedbackIconId).addClass(
              "spinner-"+spinnerType+"-"+spinnerSize);
            $(feedbackIconId).addClass('form-spinner-sm');
          }
          if(spinnerColor){
            $(feedbackIconId).addClass('text-'+spinnerColor); 
          }       
          $(feedbackIconId).addClass('form-control-feedback');
          $(block1Id).addClass("has-spinner");
          $.ajax({
            type: type,
            url: urlOrRoute,
            data: data,
            success: function(response) {
              var o = jsonToObject(response);
              if(o)
                response = o;
              else{
                console.log(response);
              }
              
              $(block1Id).removeClass("has-spinner has-error has-danger has-success");
              $(feedbackIconId).remove();
              $("#errorFeedback"+id).remove();
              $("#"+id).removeClass("error");
              
              $(block1Id).addClass(response.block1);
              $(block1Id).append(response.append);
              $("#"+id).addClass(response.field);
              //$(block1Id).parents("form").addClass("was-validated");    
            }
          });
        });
      }
    }

    function ajaxDel(url, id, idHtmlToDel = "", alertIfOk = true, alertIfNotOk = true, type = 'POST', csrf = "") {
      $.ajax({
        url: url,
        type: type, //'POST',
        data: {
          id: id,
          tokencsrf: csrf
        },
        success: function(response) {
          // Removing row from HTML Table
          if (response == true || response == 1) {
            if (idHtmlToDel != "") {
              $("#" + idHtmlToDel).css('background', 'tomato');
              $("#" + idHtmlToDel).fadeOut(1000, function() { //TODO rendre parametrable 1000 
                $("#" + idHtmlToDel).remove();
              });
            }
            if (alertIfOk)
              swalModalSingle(swalAfterConfirmed());
          } //else  alert(response); // for debub
          else {
            console.log(response);
            if (alertIfNotOk)
              swalModalSingle(swalAfterError());
          }
        }
      });
    }

    function swalModal(url, id, idHtmlToDel, postOrGet = "GET", csrf = "", alertIfConfirmAndOk = true, alertIfConfirmAndNotOk = true, alertIfCanceled = true, bootstrapButtonStyling = true, confirmButtonColor2 = confirmButtonColor, confirmCancelColor2 = confirmCancelColor) { //attrAssArrOrJsonOrTitle,text="",type="",icon="",confirmButtonColor="",confirmButtonText="",cancelButtonColor="",cancelButtonText=""){

      let swalWBB; //swalWithBootstrapButtons
      if (bootstrapButtonStyling) {
        swalWBB = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-' + confirmButtonColor2,
            cancelButton: 'btn btn-' + confirmCancelColor2
          },
          buttonsStyling: false
        });
      } else
        swalWBB = Swal.mixin();

      swalWBB.fire(swalDefaul()).then((result) => {
        if (result.value == true) {
          ajaxDel(url, id, idHtmlToDel, alertIfConfirmAndOk, alertIfConfirmAndNotOk, postOrGet, csrf);
        } else if (alertIfCanceled && result.dismiss === swalWBB.DismissReason.cancel)
          swalWBB.fire(swalAfterCanceled());
      });
    }

    function swalModalSingle(data, bootstrapButtonStyling = true, confirmButtonColor2 = confirmButtonColor, confirmCancelColor2 = confirmCancelColor) {

      let swalWBB; //swalWithBootstrapButtons
      if (bootstrapButtonStyling) {
        swalWBB = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-' + confirmButtonColor2,
            cancelButton: 'btn btn-' + confirmCancelColor2
          },
          buttonsStyling: false
        });
      } else
        swalWBB = Swal.mixin();

      swalWBB.fire(data);
    }

    //TODO revoir les nom des swal...
    function swalError(text = confirmErrorText, title = confirmErrorTitle, timer = 0, iconColor = 'warning'){
      swalModalSingle(swalAfterError(text, title, timer, iconColor));
    }
    function alertError(text = confirmErrorText, title = confirmErrorTitle, timer = 0, iconColor = 'warning'){
      swalError(text, title, timer, iconColor);
    }
    function swalModal2(attrAssArrOrJson, partIfConfirmed = "", partIfCanceled = "", buttonStyling = true, confirmButtonColor = "", confirmCancelColor = "") { //attrAssArrOrJsonOrTitle,text="",type="",icon="",confirmButtonColor="",confirmButtonText="",cancelButtonColor="",cancelButtonText=""){

      let swalWBB; //swalWithBootstrapButtons
      if (buttonStyling) {
        swalWBB = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-' + confirmButtonColor,
            cancelButton: 'btn btn-' + confirmCancelColor
          },
          buttonsStyling: false
        });
      } else
        swalWBB = Swal.mixin();

      swalWBB.fire(attrAssArrOrJson).then((result) => {
        if (partIfConfirmed || partIfCanceled) {
          if (partIfConfirmed && result.value == true)
            swalWBB.fire(partIfConfirmed);

          if (partIfCanceled && result.dismiss === swalWBB.DismissReason.cancel)
            swalWBB.fire(partIfCanceled);
        }
      });
    }

    function swalDefaul() {
      return {
        title: confirmTitle,
        text: confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: confirmConfirmButtonText,
        cancelButtonText: confirmCancelButtonText,
        reverseButtons: true
      };
    }

    function swalAfterConfirmed(timer = 0) {
      return {
        title: confirmDeletedTitle,
        text: confirmDeletedText,
        icon: 'success',
        timer: timer
      }
    }

    function swalAfterCanceled(timer = 0) {
      return {
        title: confirmCancelledTitle,
        text: confirmCancelledText,
        icon: 'error',
        timer: timer
      }
    }

    function swalAfterError(text = confirmErrorText, title = confirmErrorTitle, timer = 0, iconColor = 'warning') {
      return {
        title: title,
        text: text,
        icon: iconColor,
        timer: timer
      }
    }

    function testDel(id) {
      Swal.fire({
        buttonsStyling: false,
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        title: 'Êtes vous sure ?',
        text: 'Vous ne pourrez pas revenir en arrière!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, supprimez-le!',
        cancelButtonText: 'Non, annulez!',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
          var ajaxDel2 = ajaxDel('/testDawa/tabledel&' + id, id)
          if (ajaxDel2) {
            Swal.fire({
              buttonsStyling: false,
              customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
              },
              title: 'Supprimé!',
              text: 'Votre fichier a été supprimé.',
              icon: 'success'
            })
          } else {
            Swal.fire({
              buttonsStyling: false,
              customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
              },
              title: 'Erreur',
              text: 'La suppression n\'a pas about!',
              icon: 'warning'
            })
          }
        }
        if (result.dismiss === Swal.DismissReason.cancel) {
          Swal.fire({
            buttonsStyling: false,
            customClass: {
              confirmButton: 'btn btn-success',
              cancelButton: 'btn btn-danger'
            },
            title: 'Annulé',
            text: 'Votre enregistrement est en sécurité :)',
            icon: 'error'
          })
        }
      })
    }

    function delete2sfsq(url, id) {
      Swal.fire({
        buttonsStyling: false,
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        title: 'Êtes vous sure ?',
        text: 'Vous ne pourrez pas revenir en arrière!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, supprimez-le!',
        cancelButtonText: 'Non, annulez!',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
          if (ajaxDel(url, id)) {
            Swal.fire({
              buttonsStyling: false,
              customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
              },
              title: 'Supprimé!',
              text: 'Votre fichier a été supprimé.',
              icon: 'success'
            })
          } else {
            Swal.fire({
              buttonsStyling: false,
              customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
              },
              title: 'Supprimé!',
              text: 'La suppression n\'a pas about!',
              icon: 'success'
            })
          }
        }
        if (result.dismiss === Swal.DismissReason.cancel) {
          Swal.fire({
            buttonsStyling: false,
            customClass: {
              confirmButton: 'btn btn-success',
              cancelButton: 'btn btn-danger'
            },
            title: 'Annulé',
            text: 'Votre enregistrement est en sécurité :)',
            icon: 'error'
          })
        }
      })
    }

    function aaaa(id) {
      alert(id);
    }

    function testsw3() {
      Swal.fire({
        buttonsStyling: false,
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        title: 'Êtes vous sure ?',
        text: 'Vous ne pourrez pas revenir en arrière!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, supprimez-le!',
        cancelButtonText: 'Non, annulez!',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
          Swal.fire({
            buttonsStyling: false,
            customClass: {
              confirmButton: 'btn btn-success',
              cancelButton: 'btn btn-danger'
            },
            title: 'Supprimé!',
            text: 'Votre fichier a été supprimé.',
            icon: 'success'
          })
        }
        if (result.dismiss === Swal.DismissReason.cancel) {
          Swal.fire({
            buttonsStyling: false,
            customClass: {
              confirmButton: 'btn btn-success',
              cancelButton: 'btn btn-danger'
            },
            title: 'Annulé',
            text: 'Votre enregistrement est en sécurité :)',
            icon: 'error'
          })
        }
      })
    }

    function testsw2() {
      Swal.fire({
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false,
        title: 'Êtes vous sure ?',
        text: 'Vous ne pourrez pas revenir en arrière!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, supprimez-le!',
        cancelButtonText: 'Non, annulez!',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
          Swal.fire(
            'Supprimé!', 'Votre fichier a été supprimé.', 'success')
        }
        if (result.dismiss === Swal.DismissReason.cancel) {
          Swal.fire(
            'Annulé', 'Votre enregistrement est en sécurité :)', 'error')
        }
      })
    }

    function testsw() {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      })

      swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
          swalWithBootstrapButtons.fire(
            'Deleted!',
            'Your file has been deleted.',
            'success'
          )
        } else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {
          swalWithBootstrapButtons.fire(
            'Cancelled',
            'Your imaginary file is safe :)',
            'error'
          )
        }
      })
    }
  </script>
  
  <!-- <link href="tools/assets/css/typeahead.bundle.normalize.min.css" rel="stylesheet" /> -->

  <style>
    .showLabel{
      padding:10px; 
      font-size: 1rem; 
      text-align : right;
    }
    .showValue{
      padding:10px; 
      font-size: 1rem;
      font-weight: 400;
    }
  </style>
</head>

<!-- //TODO class="sidebar-mini" -->

<body <?= bgBlack() ?> style="background-color:#f2f9f2; font-size: 1em;">
  <div class="wrapper "><?php
   if(navbarSideTable){ ?>
    <div class="sidebar" data-color="purple" data-background-color="black" data-image="tools/assets/img/sidebar-1.jpg">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
        Tip 2: data-background-color="wite | black" 
      -->
      <div class="logo">
        <a href="<?=home()?>" class="simple-text logo-mini">
          
        </a>

        <a href="<?=home()?>" class="simple-text logo-normal" style="font-size: 12px; text-align:center">
           
        </a>
      </div>
      <div class="sidebar-wrapper"><?php
       if(navbarSideUser){ ?>
        <div class="user">
          <div class="photo">
            <img src="tools/assets/img/marc.jpg">
          </div>
          <div class="user-info">
            <a data-toggle="collapse" href="#collapseExample" class="collapsed">
              <span>
                Tania Andrew
                <b class="caret"></b>
              </span>
            </a>
            <div class="clearfix"></div>
            <div class="collapse" id="collapseExample">
              <ul class="nav">
                <li>
                  <a class="nav-link" href="#">
                    <span class="sidebar-mini"> MP </span>
                    <span class="sidebar-normal"> My Profile </span>
                  </a>
                </li>
                <li>
                  <a class="nav-link" href="#">
                    <span class="sidebar-mini"> EP </span>
                    <span class="sidebar-normal"> Edit Profile </span>
                  </a>
                </li>
                <li>
                  <a class="nav-link" href="#">
                    <span class="sidebar-mini"> S </span>
                    <span class="sidebar-normal"> Settings </span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div><?php
       }?>
        <!-- <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="./dashboard.html">
              <i class="material-icons">dashboard</i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./user.html">
              <i class="material-icons">person</i>
              <p>User Profile</p>
            </a>
          </li>
          <li class="nav-item">
              <a data-toggle="collapse" href="#pagesExamples" class="nav-link collapsed" aria-expanded="false">
                  <i class="material-icons">image</i>
                  <p> Pages 
                      <b class="caret"></b>
                  </p>
              </a>

              <div class="collapse" id="pagesExamples" aria-expanded="false">
                  <ul class="nav">
                      <li class="nav-item">
                          <a class="nav-link" href="./pages/pricing.html">
                              <span class="sidebar-mini"> P </span>
                              <span class="sidebar-normal"> Pricing </span>
                          </a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link" href="./pages/rtl.html">
                              <span class="sidebar-mini"> RS </span>
                              <span class="sidebar-normal"> RTL Support </span>
                          </a>
                      </li>
                      <li>
                          <li class="nav-item">
                              <a data-toggle="collapse" href="#pagesExamples2" class="nav-link collapsed" aria-expanded="false">
                                  <i class="material-icons">image</i>
                                  <p> Pages 
                                    <b class="caret"></b>
                                  </p>
                              </a>
                              <div class="collapse" id="pagesExamples2" aria-expanded="false" style="">
                                  <ul class="nav">
                                      <li>
                                          <a class="nav-link" href="./pages/pricing.html">
                                              <span class="sidebar-mini"> P </span>
                                              <span class="sidebar-normal"> Pricing </span>
                                          </a>
                                      </li>
                                      <li class="nav-item">
                                          <a class="nav-link" href="./pages/rtl.html">
                                              <span class="sidebar-mini"> RS </span>
                                              <span class="sidebar-normal"> RTL Support </span>
                                          </a>
                                      </li>
                                      <li>
                                          <a class="nav-link" href="./pages/timeline.html">
                                              <span class="sidebar-mini"> T </span>
                                              <span class="sidebar-normal"> Timeline </span>
                                          </a>
                                      </li>
                                  </ul>
                              </div>
                          </li>
                      </li>
                  </ul>
              </div>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./tables.html">
              <i class="material-icons">content_paste</i>
              <p>Table List</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./typography.html">
              <i class="material-icons">library_books</i>
              <p>Typography</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./icons.html">
              <i class="material-icons">bubble_chart</i>
              <p>Icons</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./map.html">
              <i class="material-icons">location_ons</i>
              <p>Maps</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./notifications.html">
              <i class="material-icons">notifications</i>
              <p>Notifications</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./rtl.html">
              <i class="material-icons">language</i>
              <p>RTL Support</p>
            </a>
          </li>
          <li class="nav-item active-pro ">
            <a class="nav-link" href="./upgrade.html">
              <i class="material-icons">unarchive</i>
              <p>Upgrade to PRO</p>
            </a>
          </li>
        </ul> -->
        <?php 
          if( ! loginRequired || userId())
            buildMenu(navbarSideTable, 0);
        ?>
      </div>
    </div><?php
   }?>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <a class="navbar-brand" href="javascript:;">Tableau de bord</a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
              <?php //authentification and registration part
              if (userId()) { ?>
                <li class="nav-item dropdown">
                  <a class="nav-link" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <? echo user() ?>
                    <i class="material-icons">person</i>
                    <p class="d-lg-none d-md-block">
                      Account
                    </p>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile"> 
                    <a class="dropdown-item" href="#">Detailles</a>
                    <a class="dropdown-item" href="#">Parametres</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?= route("userLogOut") ?>"><?= msgs["logOut"] ?></a>
                  </div>
                </li> <?php
                    } else {  ?>
                <li class="nav-item dropdown">
                  <a href="<?= route("userLogIn") ?>" class="nav-link">
                    <?= msgs["logIn"] ?>
                  </a>
                </li><?php
                      if (registrationFree) { ?>
                  <li class="nav-item dropdown">
                    <a href="<?= route("userRegistration") ?>" class="nav-link">
                      <?= msgs["registration"] ?>
                    </a>
                  </li><?php
                      }
                    } ?>

            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <!-- <main class="col-12 col-md-9 col-xl-8 py-md-3 pl-md-5 bd-content" role="main"> -->
          <?= $content ?><br>
          <!-- </main> -->
        </div>
      </div>
    </div>
  </div>
  <!--   Core JS Files   -->

  
  <script src="tools/assets/js/core/popper.min.js"></script>
  <!-- <script src="tools/assets/js/plugins/popper.min.js" type="text/javascript"></script> -->
  <script src="tools/assets/js/core/bootstrap-material-design.min.js"></script>
  <script src="tools/assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <!-- Plugin for the momentJs  -->
  <script src="tools/assets/js/plugins/moment.min.js"></script>
  <!--  Plugin for Sweet Alert -->
  <!-- <script src="tools/assets/js/plugins/sweetalert2.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  <!-- Forms Validations Plugin -->
  <script src="tools/assets/js/plugins/jquery.validate.min.js"></script>
  <!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
  <script src="tools/assets/js/plugins/jquery.bootstrap-wizard.js"></script>
  <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
  <script src="tools/assets/js/plugins/bootstrap-selectpicker.js"></script>
  <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="tools/assets/js/plugins/bootstrap-datetimepicker.min.js"></script>
  <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
  <script src="tools/assets/js/plugins/jquery.dataTables.min.js"></script>
  <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
  <script src="tools/assets/js/plugins/bootstrap-tagsinput.js"></script>
  <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
  <script src="tools/assets/js/plugins/jasny-bootstrap.min.js"></script>
  <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
  <script src="tools/assets/js/plugins/fullcalendar.min.js"></script>
  <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
  <script src="tools/assets/js/plugins/jquery-jvectormap.js"></script>
  <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
  <script src="tools/assets/js/plugins/nouislider.min.js"></script>
  <!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
  <!-- Library for adding dinamically elements -->
  <script src="tools/assets/js/plugins/arrive.min.js"></script>
  <!--  Google Maps Plugin    -->
  <!-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script> -->
  <!-- Chartist JS -->
  <script src="tools/assets/js/plugins/chartist.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="tools/assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="tools/assets/js/material-dashboard.js?v=2.1.2" type="text/javascript"></script>
  
  
<script>
  /* $('#scrollable-dropdown-menu #search').typeahead(
    //null,
    { hint: false, highlight: true, minLength: 1 },
    {
      name: 'name',
      display: 'student_name',
      limit: 10,
      maxItem: 5,
      source: function show(q, cb, cba) {
        $.ajax({ url: "< ?=route("test")?>&q="+q })
        .done(function(res) {
          cba(jsonToObject(res));
        })
        .fail(function(err) {
          alert(err);
        });
      },
      templates: {
        empty: [
          '<div class="empty-message">',
            'No data',
          '</div>'
        ].join('\n'),
        suggestion: function(data) {
          return '<div><strong>' + data.student_name + '</strong> - <img height:"50px" width:"50px" src='+data.image+'></div>';
        }
      }
  });
   */
  /* $(document).ready(function(){
    var results = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      // `states` is an array of state names defined in "The Basics"
      // local: states
      remote: {
        url: "< ?=route("test")?>&q={query}",
        wildcard: "{query}"
      }
    });
    results.initialize();
    $('#search').typeahead( 
      { hint: true,
        highlight: true,
        minLength: 1   
      },
      { name: 'name',
        diplayKey: 'student_name',
        limit: 10,
        maxItem: 5,
        source: results.ttAdapter(), //substringMatcher(states)
        templates: {
          empty: [
            '<div class="empty-message">',
              'unable to find any Best Picture winners that match the current query',
            '</div>'
          ].join('\n'),
          suggestion: function(users) {
            return '<div><strong>' + users.student_name + '</strong> - <img height:"50px" width:"50px" src='+users.image+'></div>';
          }
          // suggestion: Handlebars.compile('<div><img src="'+data.image+'"> – '+data.student_name+'</div>');
        }
      }
    );
  });
 */
</script><!-- <script src="https://demos.creative-tim.com/material-dashboard/assets/js/material-dashboard.min.js?v=2.1.2"></script> -->
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <!-- <script src="tools/assets/demo/demo.js"></script> -->
  <!-- <script>
    $(document).ready(function() {
      $().ready(function() {
        $sidebar = $('.sidebar');

        $sidebar_img_container = $sidebar.find('.sidebar-background');

        $full_page = $('.full-page');

        $sidebar_responsive = $('body > .navbar-collapse');

        window_width = $(window).width();

        fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

        if (window_width > 767 && fixed_plugin_open == 'Dashboard') {
          if ($('.fixed-plugin .dropdown').hasClass('show-dropdown')) {
            $('.fixed-plugin .dropdown').addClass('open');
          }

        }

        $('.fixed-plugin a').click(function(event) {
          // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
          if ($(this).hasClass('switch-trigger')) {
            if (event.stopPropagation) {
              event.stopPropagation();
            } else if (window.event) {
              window.event.cancelBubble = true;
            }
          }
        });

        $('.fixed-plugin .active-color span').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-color', new_color);
          }

          if ($full_page.length != 0) {
            $full_page.attr('filter-color', new_color);
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.attr('data-color', new_color);
          }
        });

        $('.fixed-plugin .background-color .badge').click(function() {
          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('background-color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-background-color', new_color);
          }
        });

        $('.fixed-plugin .img-holder').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).parent('li').siblings().removeClass('active');
          $(this).parent('li').addClass('active');


          var new_image = $(this).find("img").attr('src');

          if ($sidebar_img_container.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            $sidebar_img_container.fadeOut('fast', function() {
              $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
              $sidebar_img_container.fadeIn('fast');
            });
          }

          if ($full_page_background.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $full_page_background.fadeOut('fast', function() {
              $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
              $full_page_background.fadeIn('fast');
            });
          }

          if ($('.switch-sidebar-image input:checked').length == 0) {
            var new_image = $('.fixed-plugin li.active .img-holder').find("img").attr('src');
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
            $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.css('background-image', 'url("' + new_image + '")');
          }
        });

        $('.switch-sidebar-image input').change(function() {
          $full_page_background = $('.full-page-background');

          $input = $(this);

          if ($input.is(':checked')) {
            if ($sidebar_img_container.length != 0) {
              $sidebar_img_container.fadeIn('fast');
              $sidebar.attr('data-image', '#');
            }

            if ($full_page_background.length != 0) {
              $full_page_background.fadeIn('fast');
              $full_page.attr('data-image', '#');
            }

            background_image = true;
          } else {
            if ($sidebar_img_container.length != 0) {
              $sidebar.removeAttr('data-image');
              $sidebar_img_container.fadeOut('fast');
            }

            if ($full_page_background.length != 0) {
              $full_page.removeAttr('data-image', '#');
              $full_page_background.fadeOut('fast');
            }

            background_image = false;
          }
        });

        $('.switch-sidebar-mini input').change(function() {
          $body = $('body');

          $input = $(this);

          if (md.misc.sidebar_mini_active == true) {
            $('body').removeClass('sidebar-mini');
            md.misc.sidebar_mini_active = false;

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();

          } else {

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar('destroy');

            setTimeout(function() {
              $('body').addClass('sidebar-mini');

              md.misc.sidebar_mini_active = true;
            }, 300);
          }

          // we simulate the window Resize so the charts will get updated in realtime.
          var simulateWindowResize = setInterval(function() {
            window.dispatchEvent(new Event('resize'));
          }, 180);

          // we stop the simulation of Window Resize after the animations are completed
          setTimeout(function() {
            clearInterval(simulateWindowResize);
          }, 1000);

        });
      });
    });
  </script> -->

  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
      md.initDashboardPageCharts();
      //md.showNotification('Salam');

    });
  </script>
</body>

</html>