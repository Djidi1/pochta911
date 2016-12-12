<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'useredit']">

        <h2>Профиль клиента:</h2>
        <form action="/admin/userUpdate-{user/user_id}/" method="post" name="main_form">
            <div class="row">
                <div class="col-md-1"> </div>
                <div class="col-md-5">

                    <div class="panel panel-success">
                        <div class="panel-heading"><strong>Контакты</strong></div>
                        <div class="panel-body">
                            <input type="hidden" name="user_id" value="{user/user_id}"/>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>Название:</td>
                                        <td>
                                            <input class="form-control" type="text" name="title"
                                                   value="{user/title}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Контактное лицо:</td>
                                        <td>
                                            <input class="form-control" type="text" name="username"
                                                   value="{user/name}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>E-mail:</td>
                                        <td>
                                            <input class="form-control" type="email" name="email" id="email"
                                                   value="{user/email}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Телефон:</td>
                                        <td>
                                            <input class="form-control" type="phone" name="phone"
                                                   value="{user/phone}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Уведомления:</td>
                                        <td>
                                            <input class="form-control" type="phone" name="phone_mess"
                                                   value="{user/phone_mess}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Логин:</td>
                                        <td>
                                            <input class="form-control" type="text" name="login" id="login"
                                                   value="{user/login}" size="30">
                                                <xsl:if test="user/login != ''">
                                                    <xsl:attribute name="readonly">readonly</xsl:attribute>
                                                </xsl:if>
                                            </input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Пароль:</td>
                                        <td>
                                            <input class="form-control" type="password" name="pass" id="pass"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Группа:</td>
                                        <td>
                                            <select class="form-control" name="group_id">
                                                <xsl:for-each select="groups/item">
                                                    <option value="{id}">
                                                        <xsl:if test="id = //user/group_id">
                                                            <xsl:attribute name="selected">selected
                                                            </xsl:attribute>
                                                        </xsl:if>
                                                        <xsl:value-of select="name"/>
                                                    </option>
                                                </xsl:for-each>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Заблокировать:</td>
                                        <td>
                                            <input type="hidden" name="isBan" value="0"/>
                                            <input type="checkbox" name="isBan" value="1" id="isBan"/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!--<font color="red">* Поля обязательны для заполнения.</font>-->
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="panel panel-info">
                        <div class="panel-heading"><strong>Адреса</strong></div>
                        <div class="panel-body">
                            <xsl:for-each select="address/item">
                                <div class="input-group" rel="{position()}">
                                    <span class="input-group-addon">
                                        <xsl:value-of select="position()"/>
                                    </span>
                                    <input type="text" class="form-control" name="address[]" placeholder="Адрес" value="{address}" />
                                    <div class="input-group-btn">
                                        <button type="button" class="btn-clone btn btn-success" title="Добавить" onclick="clone_div_row(this)">
                                            <xsl:if test="position() != count(../../address/item)">
                                                <xsl:attribute name="disabled"> </xsl:attribute>
                                            </xsl:if>+</button>
                                        <button type="button" class="btn-delete btn btn-danger" title="Удалить" onclick="delete_div_row(this)">
                                            <xsl:if test="position() = 1">
                                                <xsl:attribute name="disabled"> </xsl:attribute>
                                            </xsl:if>-</button>
                                    </div>
                                </div>
                            </xsl:for-each>
                        </div>
                    </div>

                    <div class="panel panel-warning">
                        <div class="panel-heading"><strong>Кредитные карты</strong></div>
                        <div class="panel-body">
                            <xsl:for-each select="cards/item">
                                <div class="input-group" rel="{position()}">
                                    <span class="input-group-addon">
                                        <xsl:value-of select="position()"/>
                                    </span>
                                    <input type="text" class="form-control" name="credit_card[]" placeholder="Номер кредитной карты" size="20" value="{card_num}"/>
                                    <div class="input-group-btn">
                                        <button type="button" class="btn-clone btn btn-success" title="Добавить" onclick="clone_div_row(this)">
                                            <xsl:if test="position() != count(../../cards/item)">
                                                <xsl:attribute name="disabled"> </xsl:attribute>
                                            </xsl:if>+</button>
                                        <button type="button" class="btn-delete btn btn-danger" title="Удалить" onclick="delete_div_row(this)">
                                            <xsl:if test="position() = 1">
                                                <xsl:attribute name="disabled"> </xsl:attribute>
                                            </xsl:if>-</button>
                                    </div>
                                </div>
                            </xsl:for-each>
                        </div>
                    </div>

                </div>
            </div>
            <div style="text-align: center">
                <input class="btn btn-success" type="submit" value="сохранить" name="submit"/>
            </div>
        </form>

    </xsl:template>
</xsl:stylesheet>
