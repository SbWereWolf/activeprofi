/*
 * © SbWereWolf activeprofi
 * 2019.06.29
 */

const settings = {
    capacity: 10,
    pagesBefore: 5,
    pagesAfter: 5,
    placesGap: 2,
};

async function loadPage(page, capacity) {
    $.ajax({
        type: "GET",
        url: `/api/v1/task/list/${page}/${capacity}/`,
        dataType: "json",
        success: renderTasks,
        error: function (jqXHR, textStatus, errorThrown) {
        },
        timeout: 500,
    });
}

async function loadPaging(page, capacity) {
    $.ajax({
        type: "GET",
        url: `/api/v1/task/list/amount/`,
        dataType: "json",
        success: function (data) {
            renderPaging(page, capacity, data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
        },
        timeout: 500,
    });
}

function extractPagesAmount(capacity, taskCount) {
    let isSuccess = taskCount.hasOwnProperty(0);
    let element;
    if (isSuccess) {
        element = taskCount[0];
        isSuccess = element.hasOwnProperty("amount");
    }
    let amount;
    if (isSuccess) {
        amount = element.amount;
    }

    let pagesLimit = 0;
    let roundLimit = 0;
    if (isSuccess) {
        pagesLimit = amount / capacity;
        roundLimit = Math.round(amount / capacity);
    }
    if (pagesLimit > roundLimit) {
        roundLimit = roundLimit + 1;
    }
    return roundLimit;
}

function placeOrdinal(pageIndex, printedPages, row) {
    row.append($("<td>").text(pageIndex + 1).attr("data-page", pageIndex));
    printedPages.push(pageIndex);
}

function placeCurrent(page, printedPages, row) {
    row.append($("<td>").text(page + 1).attr("data-page", page).addClass("bg-success"));
    printedPages.push(page);

    return true;
}

function renderPaging(page, capacity, taskCount) {

    page = parseInt(page);
    capacity = parseInt(capacity);

    const target = $('#paging');
    target.empty();

    let roundLimit = extractPagesAmount(capacity, taskCount);
    let isSuccess = typeof roundLimit !== typeof undefined;

    let toStart = false;
    let toFinish = false;
    if (isSuccess) {
        toStart = page - settings.placesGap - settings.pagesBefore > 0;
        toFinish = page + settings.placesGap + settings.pagesAfter < roundLimit - 1;
    }

    let row = $("<tr>");
    const printedPages = [];

    if (isSuccess && toStart) {
        placeOrdinal(0, printedPages, row);
        row.append($("<td>").text(".."));
    }
    let pageOccur = false;
    if (isSuccess && !toStart) {
        for (let index = 0; index < 2; index++) {
            const addLink = (index < roundLimit) && !printedPages.includes(index);
            if (addLink && page !== index) {
                placeOrdinal(index, printedPages, row);
            }
            if (addLink && page === index && !printedPages.includes(index)) {
                pageOccur = placeCurrent(page, printedPages, row);
            }
        }
    }

    if (isSuccess) {
        for (let index = 0; index < settings.pagesBefore; index++) {
            const currentPage = index - settings.pagesBefore + page;
            const addLink = (currentPage < roundLimit) && (currentPage > -1) && !printedPages.includes(currentPage);
            if (addLink) {
                placeOrdinal(currentPage, printedPages, row);
            }
        }
    }
    if (isSuccess && !pageOccur && !printedPages.includes(page)) {
        pageOccur = placeCurrent(page, printedPages, row);
    }
    if (isSuccess) {
        for (let index = 1; index < settings.pagesAfter + 1; index++) {
            const currentPage = index + settings.pagesBefore - settings.pagesAfter + page;
            const addLink = (currentPage < roundLimit) && (currentPage > -1) && !printedPages.includes(currentPage);
            if (addLink) {
                placeOrdinal(currentPage, printedPages, row);
            }
        }
    }

    if (isSuccess && toFinish && !printedPages.includes(roundLimit - 1) && !printedPages.includes(roundLimit - 2)) {
        row.append($("<td>").text(".."));
        printedPages.push(roundLimit - 2);
        placeOrdinal(roundLimit - 1, printedPages, row);
    }

    if (isSuccess && !toFinish) {
        for (let index = 0; index < 2; index++) {
            const pageIndex = (roundLimit - 2 + index);
            const addLink = (pageIndex < roundLimit) && !printedPages.includes(pageIndex);
            if (addLink && page !== index) {
                placeOrdinal(pageIndex, printedPages, row);
            }
            if (addLink && page === pageIndex && !printedPages.includes(pageIndex)) {
                placeCurrent(page, printedPages, row);
            }
        }
    }

    if (isSuccess) {
        const table = $('<table>')
            .addClass("table table-hover-cells table-bordered")
            .append($("<tbody>").append(row));

        target.html(table);

        $.each($('.table-hover-cells tbody td').get()
            , (index, object) => {
                $(object).on('click', function () {
                    const page = $(this).attr("data-page");
                    const isSuccess = typeof page !== typeof undefined;
                    if (isSuccess) {
                        loadPaging(page, settings.capacity);
                        loadPage(page, settings.capacity);
                    }
                })
            });
    }
}


function renderTasks(data) {
    const table = $('<table>').addClass("table table-hover table-bordered");
    table.append(
        $('<thead>').append(
            $('<tr>')
                .append($('<th>').text('Номер задачи'))
                .append($('<th>').text('Заголовок'))
                .append($('<th>').text('Дата выполнения'))
        )
    );
    const content = $('<tbody>');
    $.each(data, function (key, value) {
        const id = value.id;
        content.append(
            $('<tr>').attr("data-id", id)
                .append($('<td>').text(id))
                .append($('<td>').text(value.title))
                .append($('<td>').text(value.date))
        );
    });
    const target = $('#taskList');
    target.empty();
    target.html(table.append(content));

    $.each($('.table-hover tbody tr').get()
        , (index, object) => {
            $(object).on('click', function () {
                const id = $(this).attr("data-id");
                getTask(id);
            })
        });
}

async function getTask(id) {
    let task;
    $.ajax({
        type: "GET",
        url: `/api/v1/task/${id}/`,
        dataType: "json",
        success: showTask,
        error: function (jqXHR, textStatus, errorThrown) {
        },
        timeout: 500,
    });
}

function showTask(data) {
    const isSuccess = data.hasOwnProperty(0);
    if (isSuccess) {
        task = data[0];

        $("#id").text(task.id);
        $("#title").val(task.title);
        $("#date").val(task.date);
        $("#author").val(task.author);
        $("#status").val(task.status);
        $("#description").val(task.description);

        $("#detailTaskView").modal();
    }
}
