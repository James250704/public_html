$(document).ready(function () {
    // 新增商品
    $("#addProduct").on("click", async function () {
        try {
            const formData = new FormData();
            formData.append("action", "addProduct");
            formData.append("productName", $("#productName").val());
            formData.append("productType", $("#productType").val());
            formData.append("productIntro", $("#productIntro").val());
            formData.append(
                "productActive",
                $("#productActive").is(":checked") ? "1" : "0"
            );

            // 主圖片
            const mainFile = $("#mainImageInput")[0].files[0];
            if (mainFile) formData.append("mainImage", mainFile);

            // 詳細圖片
            const galleryFiles = $("#galleryImageInput")[0].files;
            for (let i = 0; i < galleryFiles.length; i++) {
                formData.append("galleryImages[]", galleryFiles[i]);
                // 確保圖片順序正確傳遞
                formData.append(`galleryOrders[]`, i + 2); // 主圖片order=1，詳細圖片從2開始
            }

            // 尺寸與顏色
            const sizes = [];
            $(".size-price-block").each(function () {
                const $blk = $(this);
                const sizeData = {
                    size: $blk.find(".size-input").val(),
                    sizeDescription: $blk.find(".size-desc-input").val(),
                    price: $blk.find(".price-input").val(),
                    colors: [],
                };
                $blk.find(".color-stock-container .color-row").each(
                    function () {
                        const $row = $(this);
                        sizeData.colors.push({
                            color: $row.find("input").eq(0).val(),
                            stock: $row.find("input").eq(1).val(),
                        });
                    }
                );
                sizes.push(sizeData);
            });
            // 確保尺寸和顏色選項正確傳遞
            formData.append("sizes", JSON.stringify(sizes));

            // 發送
            const res = await fetch(
                "/new_test/models/backend/functions/addProductHandler.php",
                {
                    method: "POST",
                    body: formData,
                }
            );
            const result = await res.json();
            if (result.success) {
                alert("商品新增成功");
                $("#addProductModal").modal("hide");
                filterProducts();
            } else {
                alert("商品新增失敗: " + result.message);
            }
        } catch (err) {
            console.error("新增商品錯誤:", err);
            alert("新增商品時發生錯誤");
        }
    });

    // 主圖片預覽
    $("#uploadMainImage").on("click", () => $("#mainImageInput").click());
    $("#mainImageInput").on("change", function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => {
            $("#mainImagePreview").attr("src", ev.target.result).show();
            $("#uploadMainImage").hide();
        };
        reader.readAsDataURL(file);
    });

    // 詳細圖片選擇
    $("#addGalleryImage").on("click", () => $("#galleryImageInput").click());
    $("#galleryImageInput").on("change", function (e) {
        const files = e.target.files;
        const $preview = $("#galleryImagesPreview").empty();
        for (let i = 0; i < files.length; i++) {
            const reader = new FileReader();
            reader.onload = (ev) => {
                const $img = $("<img>")
                    .attr("src", ev.target.result)
                    .addClass("img-thumbnail")
                    .css({ width: "100px", height: "100px", margin: "4px" })
                    .attr("draggable", true);
                $preview.append($img);
            };
            reader.readAsDataURL(files[i]);
        }
    });

    // 動態新增／刪除尺寸區塊
    $("#addSizeRow").on("click", function () {
        const $tpl = $(`
            <div class="card mb-3 size-price-block">
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-3">
                    <label class="form-label">尺寸</label>
                    <input type="number" class="form-control size-input" placeholder="尺寸">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">尺寸描述</label>
                    <input type="text" class="form-control size-desc-input" placeholder="尺寸描述">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">價格</label>
                    <input type="number" class="form-control price-input" placeholder="價格">
                  </div>
                  <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-outline-danger remove-size-btn">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
                <div class="mt-3">
                  <button type="button" class="btn btn-outline-primary add-color-btn">
                    <i class="bi bi-plus"></i> 添加顏色
                  </button>
                  <div class="color-stock-container mt-2">
                    <div class="color-row row g-2 mb-2">
                      <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="顏色描述">
                      </div>
                      <div class="col-md-4">
                        <input type="number" class="form-control" placeholder="庫存">
                      </div>
                      <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-color-btn" disabled>
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        `);
        $("#sizePriceContainer").append($tpl);
    });

    // 事件代理：刪除尺寸
    $(document).on("click", ".remove-size-btn", function () {
        if ($(".size-price-block").length > 1) {
            $(this).closest(".size-price-block").remove();
        }
    });

    // 事件代理：新增顏色
    $(document).on("click", ".add-color-btn", function () {
        const $container = $(this).siblings(".color-stock-container");
        const $row = $(`
            <div class="color-row row g-2 mb-2">
              <div class="col-md-6"><input type="text" class="form-control" placeholder="顏色描述"></div>
              <div class="col-md-4"><input type="number" class="form-control" placeholder="庫存"></div>
              <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-outline-danger remove-color-btn">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
        `);
        $container.append($row);
        // 只有超過一行時才啟用刪除
        if ($container.find(".color-row").length > 1) {
            $container.find(".remove-color-btn").prop("disabled", false);
        }
    });

    // 事件代理：刪除顏色
    $(document).on("click", ".remove-color-btn", function () {
        const $container = $(this).closest(".color-stock-container");
        if ($container.find(".color-row").length > 1) {
            $(this).closest(".color-row").remove();
        }
        if ($container.find(".color-row").length === 1) {
            $container.find(".remove-color-btn").prop("disabled", true);
        }
    });

    // 上架狀態顯示文字
    $("#productActive").on("change", function () {
        $("#statusText").text($(this).is(":checked") ? "上架" : "下架");
    });

    // 篩選商品
    $("#filterType, #filterStatus").on("change", filterProducts);
    $("#searchProduct").on("input", filterProducts);

    async function filterProducts() {
        const type = $("#filterType").val();
        const status = $("#filterStatus").val();
        const search = $("#searchProduct").val();
        try {
            const res = await fetch(
                `/new_test/api/product.php?action=filter&type=${encodeURIComponent(
                    type
                )}&status=${encodeURIComponent(
                    status
                )}&search=${encodeURIComponent(search)}`
            );
            const data = await res.json();
            if (!data.success) throw new Error(data.message);
            const $tbody = $("table tbody").empty();
            if (data.products.length === 0) {
                return $tbody.append(
                    '<tr><td colspan="6" class="text-center">暫無符合條件的商品</td></tr>'
                );
            }
            data.products.forEach((p) => {
                $tbody.append(`
                    <tr>
                      <td>${p.ProductID}</td>
                      <td>${p.ProductName}</td>
                      <td>${p.Type}</td>
                      <td>${p.OptionCount}</td>
                      <td>${
                          p.isActive == 1
                              ? '<span class="badge bg-success">上架</span>'
                              : '<span class="badge bg-secondary">下架</span>'
                      }</td>
                      <td>
                        <div class="btn-group">
                          <button class="btn btn-sm btn-outline-primary edit-product" data-id="${
                              p.ProductID
                          }"><i class="bi bi-pencil"></i></button>
                          <button class="btn btn-sm btn-outline-info view-options" data-id="${
                              p.ProductID
                          }"><i class="bi bi-list-ul"></i></button>
                          <button class="btn btn-sm btn-outline-danger delete-product" data-id="${
                              p.ProductID
                          }"><i class="bi bi-trash"></i></button>
                        </div>
                      </td>
                    </tr>
                `);
            });
            renderPagination(
                data.currentPage,
                data.totalPages,
                type,
                status,
                search
            );
        } catch (err) {
            console.error("篩選失敗:", err);
        }
    }

    function renderPagination(current, total, type, status, search) {
        const $pg = $(".pagination").empty();
        const makeLink = (p, text, disabled) =>
            `<li class="page-item ${
                disabled ? "disabled" : ""
            }"><a class="page-link" href="?page=${p}&type=${encodeURIComponent(
                type
            )}&status=${encodeURIComponent(status)}&search=${encodeURIComponent(
                search
            )}">${text}</a></li>`;
        $pg.append(makeLink(current - 1, "上一頁", current <= 1));
        for (let i = 1; i <= total; i++) {
            $pg.append(
                `<li class="page-item ${
                    i === current ? "active" : ""
                }"><a class="page-link" href="?page=${i}&type=${encodeURIComponent(
                    type
                )}&status=${encodeURIComponent(
                    status
                )}&search=${encodeURIComponent(search)}">${i}</a></li>`
            );
        }
        $pg.append(makeLink(current + 1, "下一頁", current >= total));
    }

    // 儲存商品（示範 Toast）
    $("#saveProduct").on("click", function () {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="d-flex">
                <div class="toast-body"><i class="bi bi-check-circle me-2"></i>商品已成功保存！</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
              </div>
            </div>`;
        const $container = $('<div class="position-fixed bottom-0 end-0 p-3">')
            .append(toastHtml)
            .appendTo("body");
        const toast = new bootstrap.Toast($container.find(".toast")[0], {
            delay: 3000,
        });
        toast.show();
        toast._element.addEventListener("hidden.bs.toast", () =>
            $container.remove()
        );
        $("#addProductModal").modal("hide");
    });
});
