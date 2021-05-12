<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 512);
            $table->tinyInteger('sale_flag')->nullable()->default(0)->comment("Quyền Sale");
            $table->tinyInteger('customer_manager_flag')->nullable()->default(0)->comment("Quyền quản lý khách hàng");
            $table->tinyInteger('view_revenue_sale_flag')->nullable()->default(0)->comment("Xem doanh thu SALE");
            $table->tinyInteger('share_orders_flag')->nullable()->default(0)->comment("Chia đơn");
            $table->tinyInteger('bung_don_flag')->nullable()->default(0)->comment("Bung đơn");
            $table->tinyInteger('bung_don2_flag')->nullable()->default(0)->comment("Bung đơn (Không bung thành công và chuyển hàng)");
            $table->tinyInteger('customer_care_flag')->nullable()->default(0)->comment("Bung đơn (Không bung thành công và chuyển hàng)");
            $table->tinyInteger('marketing_flag')->nullable()->default(0)->comment("Quyền Marketing");
            $table->tinyInteger('marketing_manager_flag')->nullable()->default(0)->comment("Quyền quản lý Marketing");
            $table->tinyInteger('view_revenue_marketing_flag')->nullable()->default(0)->comment("Xem doanh thu Marketing");
            $table->tinyInteger('export_excel_flag')->nullable()->default(0)->comment("Xuất excel");
            $table->tinyInteger('accountant_flag')->nullable()->default(0)->comment("Kế toán");
            $table->tinyInteger('stock_out_flag')->nullable()->default(0)->comment("Xuất kho");
            $table->tinyInteger('stock_manager_flag')->nullable()->default(0)->comment("Quản lý kho");
            $table->string('status_permissions')->nullable()->default(null)->comment("Quyền trạng thái: Dạng JSON");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}