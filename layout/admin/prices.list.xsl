<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'priceslist']">
		<h2>Стоимость за киллометр</h2>
		<form method="post">
			<input type="hidden" name="sub_action" value="save"/>
			<div>
				<table class="table table-hover">
				<tbody>
					<tr>
						<th>Стоимость пересечения Невы</th>
						<td><input name="km_neva" class="form-control" type="number" value="{prices/add_item/km_neva}"/></td>
					</tr>
					<tr>
						<th>Стоимость за пределами Санкт-Петербурга</th>
						<td><input name="km_kad" class="form-control" type="number" value="{prices/add_item/km_kad}"/></td>
					</tr>
					<tr>
						<th>Стоимость за попадание в Геозону за КАДом</th>
						<td><input name="km_geozone" class="form-control" type="number" value="{prices/add_item/km_geozone}"/></td>
					</tr>
					<tr>
						<th>Стоимость в Всеволожск</th>
						<td><input name="km_vsevol" class="form-control" type="number" value="{prices/add_item/km_vsevol}"/></td>
					</tr>
				</tbody>
				</table>
				<table class="table table-hover">
					<thead>
						<tr>
							<th>id</th>
							<th>от</th>
							<th>до</th>
							<th>стоимость</th>
						</tr>
					</thead>
					<tbody>
						<xsl:for-each select="prices/item">
							<tr>
								<td>
									<xsl:value-of select="id"/>
								</td>
								<td>
									<input name="km_from[]" class="form-control" type="number" value="{km_from}"/>
								</td>
								<td>
									<input name="km_to[]" class="form-control" type="number" value="{km_to}"/>
								</td>
								<td>
									<input name="km_cost[]" class="form-control" type="number" value="{km_cost}"/>
								</td>
							</tr>
						</xsl:for-each>
					</tbody>
				</table>
			</div>
			<input class="btn btn-success" type="submit" value="Сохранить"/>
		</form>
	</xsl:template>
</xsl:stylesheet>
