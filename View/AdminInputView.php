<?php

namespace mvc\View;

class AdminInputView
{
    private string $content;
    private $btn_name;

    public function __construct($title, $target_action, $logout_action, $description, $btn_name = "Daten hinzufügen / aktualisieren")
    {
        $this->btn_name = $btn_name;
        $this->content = <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>$title | Admin Panel | ZOW Duisburg</title>
</head>
<body>
<div class="container">
    <p class="grey-text">Dieses Panel ist nur für Administratoren, denen Aufgaben bezüglich der Webseite der ZOW 2023 zugewiesen wurden! 
     Es ist nicht für den Gebrauch durch unbefugte bestimmt. Fragen bitte in die Whatsapp Gruppe oder Privat an Jonathan Dreisvogt (+49 160 99735432)
     Falls ihnen bekannt ist, dass sich die Zugangsdaten für dieses Panel an Dritte verbreitet haben, benachrichtigen Sie umgehend einen Administrator!
     </p>
    <br><br><br>
    <h2>$title</h2>
    <div class="row">
        <div class="col s12">
            <p>$description</p>
        </div>
        <form action="$target_action" METHOD="post">
HTML;
    }

    public function addTextInput(string $return_name, string $display_name, bool $pw = false, string $description = "", int $max_length = -1)
    {
        $len_limiter = $max_length >= 0 ? "data_length=\"" . $max_length . "\" " : "";
        $type = $pw ? "password" : "text";
        $this->content .= <<<HTML
        <div class="input-field col s6">
          <input placeholder="$display_name" id="$return_name" type="$type" name="$return_name" $len_limiter>
          <label for="$return_name">$display_name</label>
          <span class="helper-text">$description</span>
        </div>
HTML;
    }

    public function addCheckbox(string $return_name, string $display_name)
    {
        $this->content .= <<<HTML
        <div class="col s6 m4">
        <p>
            <label>
                <input type="checkbox" name="$return_name"/>
                <span>$display_name</span>
          </label>
        </p>    
</div>
HTML;
    }

    public function addDateInput(string $return_name, string $display_name)
    {
        $this->content .=<<<HTML
<div class="col s12">
  <input type="text" class="datepicker" name="$return_name"/>
        <span class="helper-text">$display_name</span>
</div>
HTML;
    }

    public function addTimeInput(string $return_name, string $display_name)
    {
        $this->content .=<<<HTML
<div class="col s12">
  <input type="text" class="timepicker" name="$return_name"/>
        <span class="helper-text">$display_name</span>
</div>
HTML;
    }

    public function addSelectInput(string $return_name, string $display_name, array $options)
    {
        $option_string = "";
        foreach ($options as $name => $value) {
            $option_string .= "<option value=\"$value\"> $name </option>\n";
        }
        $this->content .=<<<HTML
<div class="col s6">
  <select name="$return_name">
  <option value="" disabled selected> $display_name </option> 
  $option_string
</select>
</div>
HTML;
    }

    public function addTextarea(string $return_name, string $display_name, string $default = "")
    {
        $this->content .=<<<HTML
<div class="col s12">
      <div class="row">
        <div class="input-field col s12">
          <textarea id="$return_name" class="materialize-textarea" name="$return_name">$default</textarea>
          <label for="$return_name">$display_name</label>
        </div>
      </div>
    </div>
HTML;
    }

    public function addNumberPicker(string $return_name, string $display_name, int $min, int $max, int $default = 60, string $description = "")
    {
        $this->content .= <<<HTML
    <div class="input-field col s6">
          <input placeholder="$display_name" id="$return_name" type="number" name="$return_name" min="$min" max="$max">
          <label for="$return_name">$display_name</label>
          <span class="helper-text">$description</span>
        </div>
HTML;
    }

    public function addTable(string $return_name, array $data)
    {
        $table = '<table>';

        $table .= '<thead><tr>';
        foreach (array_keys($data[0]) as $key) {
            if ($key == "id") continue;
            $table .= '<th>' . $key . '</th>';
        }
        $table .= '</tr></thead>';
        // Tabelleninhalt generieren
        $table .= '<tbody style="cursor:pointer">';

        foreach ($data as $row) {
            // Generiere eine eindeutige ID für jede Zeile
            $rowID = 'row_' . $row["id"];
            $table .= '<tr id="' . $rowID . '">';
            unset($row["id"]);
            foreach ($row as $value) {
                $table .= '<td><span class="truncate">' . $value . '</span></td>';
            }
            $table .= '</tr>';
        }
        $table .= '</tbody>';

        $table .= '</table>';

        $this->content .= <<<HTML
$table
<input type="hidden" name="$return_name" id="table_input">
<br><br>
HTML;

    }

    public function addInfoText($text)
    {
        $this->content .= "<p> $text </p>";
    }

    public function addHTML($html)
    {
        $this->content .= "<div>$html</div>";
    }

    private array $scripts = [];

    public function addScript($js_content)
    {
        $this->scripts[] = $js_content;
    }
    public function generateHtmlValue()
    {
        $sc_content = "";
        if(!empty($this->scripts)) $sc_content = "<script>" . implode("</script><script>", $this->scripts) . "</script>";
        return $this->content . <<<HTML
<div class="col s12">
  <button class="btn right waves-effect waves-light" type="submit" name="action">$this->btn_name
    <i class="material-icons right">upgrade</i>
  </button>
</div>
</form>
</div>
<style>
    tr.selected {
        background: black;
        color: white;
    }
    .code{
    font-family: monospace;
    padding: 10px;
    background-color: white;
    border: 2px solid black;
    color: black;
    }
</style>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    $(document).ready(function(){
    $('.datepicker').datepicker({
        format: "mm-dd-yyyy"
    });
    $('select').formSelect();
    $('.timepicker').timepicker();
    
    var rows = document.getElementsByTagName('tr');
            for (var i = 0; i < rows.length; i++) {
                rows[i].addEventListener('click', function() {
                    document.getElementById('table_input').value = this.id.substring(4);
                    for(var i = 0; i < rows.length; i++) {
                      rows[i].classList.remove('selected')
                    }
                    this.classList.add('selected');
                });
            }
  });
</script>
$sc_content
</body>
</html>
HTML;
    }
}