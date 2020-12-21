
    
     {*"ver variables actuales"{debug}*}
    <div>
    <div style="display: none;" id="hidden-content">
	<h2>Exportar combis</h2>
    <table>
    {foreach $combinaciones as $combinacion}
    <tr>
    <td>{$combinacion.Referencia}</td>
    <td><input type="checkbox" data-id-product-attribute="{$combinacion.Id}"  name ="checkcombi"></td>
    </tr>
    {/foreach}

    </table>
    <button onclick = "ejecutarAjax()">Exportar combinación/es</button>
    </div>
<div class="row">
<div class="col-md-12">
		<div class="form-group mb-4">
			<label>Enviar combinación a Portugal</label>
			<br>  
			</div>
            <div>
            
           <a href="#hidden-content" id="contenidofancy">Exportar combinaciones</a>
        
    </div>
    </div>
</div>
</div>