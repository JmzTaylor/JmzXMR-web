/*
 * Jmz XMR
 * Copyright (C) 2020  James Taylor
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(
    function () {
        refreshTable(false);
        const darkmode = new Darkmode();
        $('body').bootstrapMaterialDesign();
        $(".themeswitch").click(
            function () {
                darkmode.toggle();
            }
        );

        $(".logout").click(
            function () {
                $.ajax(
                    {
                        url: 'functions.php',
                        type: 'post',
                        data: {
                            "logout": true
                        }
                    }
                ).done(
                    function () {
                        window.location.replace("index.php");
                    }
                );
            }
        );

        $("#password").on(
            "keyup", function () {
                if ($(this).val() !== $("#password-verify").val() || $.trim(this.value).length === 0) {
                    $('.newUserButton').prop('disabled', true);
                    $("#password2").removeClass("is-valid").addClass("is-invalid");
                } else {
                    $('.newUserButton').prop('disabled', false);
                    $("#password2").removeClass("is-invalid").addClass("is-valid");
                }
            }
        );

        $("#password-verify").on(
            "keyup", function () {
                if ($("#password").val() !== $(this).val() || $.trim(this.value).length === 0) {
                    $('.newUserButton').prop('disabled', true);
                    $(this).removeClass("is-valid").addClass("is-invalid");
                } else {
                    $('.newUserButton').prop('disabled', false);
                    $(this).removeClass("is-invalid").addClass("is-valid");
                }
            }
        );
    }
);

function updateRig(id)
{
    $.ajax(
        {
            url: 'functions.php',
            type: 'post',
            data: {
                "editRigId": id,
                "session_key": $("#session_key").val(),
                "EditRigName": $('#AddRigName').val(),
                "EditRigUrl": $('#AddRigUrl').val(),
                "EditRigPort": $('#AddRigPort').val(),
                "EditRigAccessToken": $('#AddRigAccessToken').val()
            }
        }
    );
    refreshTable(true);
}

function deletePoolConfirm(id)
{
    $("#messageBody").text("Are you sure you want to delete this pool?");
    $(".deleteButtonFinal").attr("value", id).attr("onclick", "deletePool(" + id +")");
    $('#deleteModal').modal('show');
}

function deleteRigConfirm(id)
{
    $(".deleteButtonFinal").attr("id", id);
    $("#messageBody").text("Are you sure you want to delete this rig?");
    $('#deleteModal').modal('show');
}

function deletePool(id)
{
    $.ajax(
        {
            url: 'functions.php',
            type: 'post',
            data: {
                "deletePool": "true",
                "session_key": $("#session_key").val(),
                "id": id
            }
        }
    );
    refreshTable(true);
}

function deleteRig(id)
{
    $.ajax(
        {
            url: 'functions.php',
            type: 'post',
            data: {
                "deleteRig": "true",
                "session_key": $("#session_key").val(),
                "id": id
            }
        }
    );
    refreshTable(true);
}

function addRig()
{
    $.ajax(
        {
            url: 'functions.php',
            type: 'post',
            data: {
                "addRig": "true",
                "session_key": $("#session_key").val(),
                "AddRigName": $('#AddRigName').val(),
                "AddRigUrl": $('#AddRigUrl').val(),
                "AddRigPort": $('#AddRigPort').val(),
                "AddRigAccessToken": $('#AddRigAccessToken').val()
            }
        }
    );
    refreshTable(true);
}

function editRig(id)
{
    $.ajax(
        {
            url: "functions.php",
            type: "POST",
            data: {
                "getRig": "true",
                "session_key": $("#session_key").val(),
                "id": id
            },
            dataType: "JSON"
        }
    ).done(
        function (data) {
            $("#AddRigName").val(data['rigName']).trigger("change");
            $("#AddRigUrl").val(data['rigUrl']).trigger("change");
            $("#AddRigPort").val(data['rigPort']).trigger("change");
            $("#AddRigAccessToken").val(data['rigAccessToken']).trigger("change");
            $(".editSubmit").attr("value", id).attr("onclick", "updateRig(" + id +")");
            $('#addRig').modal('show');
        }
    );
}

function editPool(id)
{
    $.ajax(
        {
            url: "functions.php",
            type: "POST",
            data: {
                "getPool": "true",
                "session_key": $("#session_key").val(),
                "id": id
            },
            dataType: "JSON"
        }
    ).done(
        function (data) {
            $("#poolName").val(data['poolName']).trigger("change");
            $("#address").val(data['address']).trigger("change");
            $("#poolSelect option[value='" + id + "']").attr("selected", true);
            $(".addPoolButton").attr("value", id).attr("onclick", "updatePool(" + id +")");
            $('#addPool').modal('show');
        }
    );
}

function updatePool(id)
{
    $.ajax(
        {
            url: 'functions.php',
            type: 'post',
            data: {
                "updatePool": "true",
                "session_key": $("#session_key").val(),
                "id" : id,
                "poolName": $('#poolName').val(),
                "address": $('#address').val(),
                "poolType": $("#poolSelect :selected").val()
            }
        }
    );
    refreshTable(true);
}

function addPool()
{
    $.ajax(
        {
            url: 'functions.php',
            type: 'post',
            data: {
                "addPool": "true",
                "session_key": $("#session_key").val(),
                "poolName": $('#poolName').val(),
                "address": $('#address').val(),
                "poolType": $("#poolSelect :selected").val()
            }
        }
    );
    refreshTable(true);
}

function refreshTable(reload)
{
    Pace.start();
    $('#tableHolder').load(
        'rigdata.php', function () {
            Pace.stop();
            if (!reload) {
                setTimeout(refreshTable, 10000);
            }
        }
    );
}
