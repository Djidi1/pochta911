<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'useredit']">

        <h2>Профиль клиента:</h2>
        <form action="/admin/userUpdate-{user/user_id}/" method="post" name="main_form">
            <div class="row">
                <div class="col-md-6">

                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <strong>Контакты</strong>
                        </div>
                        <div class="panel-body">
                            <input id="user_id" type="hidden" name="user_id" value="{user/user_id}"/>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>Название:</td>
                                        <td>
                                            <input class="form-control" type="text" name="title" onkeyup="check_user(this)"
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
                                            <input class="form-control" type="text" name="login" id="login" onkeyup="check_user(this)"
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
                                    <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                                        <tr>
                                            <td>Процент инкассации:</td>
                                            <td>
                                                <input class="form-control" type="text" name="inkass_proc" id="inkass_proc" value="{user/inkass_proc}"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Заблокировать:</td>
                                            <td>
                                                <input type="hidden" name="isBan" value="0"/>
                                                <input type="checkbox" name="isBan" value="1" id="isBan"/>
                                            </td>
                                        </tr>
                                    </xsl:if>
                                </tbody>
                            </table>
                            <!--<font color="red">* Поля обязательны для заполнения.</font>-->
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <strong>Адреса</strong>
                        </div>
                        <div class="panel-body">
                            <xsl:for-each select="address/item">
                                <xsl:call-template name="address_row"/>
                            </xsl:for-each>
                            <xsl:if test="count(address/item) = 0">
                                <xsl:call-template name="address_row"/>
                            </xsl:if>
                        </div>
                    </div>

                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <strong>Оплата</strong>
                        </div>
                        <div class="panel-body">
                            <xsl:for-each select="cards/item">
                                <xsl:call-template name="pay_row"/>
                            </xsl:for-each>
                            <xsl:if test="count(cards/item) = 0">
                                <xsl:call-template name="pay_row"/>
                            </xsl:if>
                        </div>
                    </div>
                </div>
            </div>
            <div style="text-align: center">
                <input class="btn btn-success" type="submit" value="сохранить" name="submit"/>
            </div>
        </form>
        <hr/>
    </xsl:template>

    <xsl:template name="address_row">
        <div class="input-group" rel="{position()}">
            <span class="input-group-addon">
                <xsl:value-of select="position()"/>
            </span>
            <input id="address" type="text" class="form-control address" name="address[]" placeholder="Адрес" value="{address}"/>
            <br/>
            <textarea name="addr_comment[]" class="form-control">
                <xsl:value-of select="comment"/>
            </textarea>
            <div class="input-group-btn" style="vertical-align: top;">
                <button type="button" class="btn-clone btn btn-success" title="Добавить" onclick="clone_div_row($(this).parent().parent())">
                    <xsl:if test="count(../../address/item) = 0 or position() != count(../../address/item)">
                        <xsl:attribute name="disabled"> </xsl:attribute>
                    </xsl:if>
                    +
                </button>
                <button type="button" class="btn-delete btn btn-danger" title="Удалить" onclick="delete_div_row(this)">
                    <xsl:if test="position() = 1">
                        <xsl:attribute name="disabled"> </xsl:attribute>
                    </xsl:if>
                    -
                </button>
            </div>
        </div>
    </xsl:template>
    <xsl:template name="pay_row">
        <div class="input-group" rel="{position()}">
            <span class="input-group-addon">
                <xsl:value-of select="position()"/>
            </span>
            <input type="text" class="form-control" name="credit_card[]" placeholder="Номер кредитной карты" size="20" value="{card_num}"/>
            <br/>
            <textarea name="card_comment[]" class="form-control">
                <xsl:value-of select="comment"/>
            </textarea>
            <div class="input-group-btn" style="vertical-align: top;">
                <button type="button" class="btn-clone btn btn-success" title="Добавить" onclick="clone_div_row($(this).parent().parent())">
                    <xsl:if test="count(../../cards/item) = 0 or position() != count(../../cards/item)">
                        <xsl:attribute name="disabled"> </xsl:attribute>
                    </xsl:if>
                    +
                </button>
                <button type="button" class="btn-delete btn btn-danger" title="Удалить" onclick="delete_div_row(this)">
                    <xsl:if test="position() = 1">
                        <xsl:attribute name="disabled"> </xsl:attribute>
                    </xsl:if>
                    -
                </button>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>
