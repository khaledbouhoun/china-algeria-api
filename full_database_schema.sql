CREATE SCHEMA "public";
CREATE TYPE "status_type" AS ENUM('ITEM', 'PACKAGE_ITEM', 'PACKAGE', 'INSPECTION');
CREATE TYPE "User_Status" AS ENUM('Enable', 'Disable', 'Created', 'Deleted');
CREATE TYPE "zone_type" AS ENUM('ZONE_A', 'ZONE_B', 'ZONE_C', 'EVERYWHERE');
CREATE TABLE "countries" (
	"id" bigserial PRIMARY KEY,
	"country" varchar(255) NOT NULL CONSTRAINT "countries_country_key" UNIQUE
);
CREATE TABLE "order_item_images" (
	"id" bigserial PRIMARY KEY,
	"order_item_id" bigint NOT NULL,
	"image_path" varchar(255) NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
CREATE TABLE "order_item_steps" (
	"id" bigserial PRIMARY KEY,
	"item_id" bigint NOT NULL,
	"status_id" bigint NOT NULL,
	"zone_id" bigint,
	"user_id" bigint,
	"comment" text,
	"created_by" bigint,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
CREATE TABLE "order_items" (
	"id" bigserial PRIMARY KEY,
	"public_code" varchar(50) NOT NULL CONSTRAINT "order_items_public_code_key" UNIQUE,
	"order_id" bigint NOT NULL,
	"designation" varchar(255) NOT NULL,
	"quantity_declared" integer NOT NULL,
	"price_unit_declared" numeric(14, 2) DEFAULT '0' NOT NULL,
	"weight_unit_declared" numeric(10, 3) DEFAULT '0' NOT NULL,
	"weight_total" numeric(10, 3) DEFAULT '0' NOT NULL,
	"current_step_id" bigint NOT NULL,
	"comment" text,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL,
	"deleted_at" timestamp with time zone
);
CREATE TABLE "orders" (
	"id" bigserial PRIMARY KEY,
	"client_id" bigint NOT NULL,
	"comment" text,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL,
	"deleted_at" timestamp with time zone
);
CREATE TABLE "package_item_receptions" (
	"id" bigserial PRIMARY KEY,
	"package_item_id" bigint NOT NULL,
	"inspected_by" bigint NOT NULL,
	"expected_quantity" integer NOT NULL,
	"expected_weight" numeric(10, 3) DEFAULT '0',
	"received_quantity" integer NOT NULL,
	"received_weight" numeric(10, 3) DEFAULT '0',
	"difference_quantity" integer GENERATED ALWAYS AS ((received_quantity - expected_quantity)) STORED,
	"difference_weight" numeric(10, 3) GENERATED ALWAYS AS ((received_weight - expected_weight)) STORED,
	"count_reception" integer DEFAULT 0,
	"comment" text,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
CREATE TABLE "package_item_steps" (
	"id" bigserial PRIMARY KEY,
	"package_item_id" bigint NOT NULL,
	"status_id" bigint NOT NULL,
	"zone_id" bigint,
	"user_id" bigint,
	"comment" text,
	"created_by" bigint,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
CREATE TABLE "package_items" (
	"id" bigserial PRIMARY KEY,
	"package_id" bigint NOT NULL,
	"order_item_id" bigint NOT NULL,
	"quantity_allocated" integer NOT NULL,
	"weight_total_allocated" numeric(10, 3) DEFAULT '0' NOT NULL,
	"amount_total_allocated" numeric(14, 2) DEFAULT '0' NOT NULL,
	"current_step_id" bigint NOT NULL,
	"quantity_recupered" integer NOT NULL,
	"weight_total_recupered" numeric(10, 3) DEFAULT '0' NOT NULL,
	"amount_total_recupered" numeric(14, 2) DEFAULT '0' NOT NULL,
	"created_by" bigint,
	"updated_by" bigint,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
CREATE TABLE "package_steps" (
	"id" bigserial PRIMARY KEY,
	"package_id" bigint NOT NULL,
	"status_id" bigint NOT NULL,
	"zone_id" bigint,
	"user_id" bigint,
	"comment" text,
	"created_by" bigint,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
CREATE TABLE "packages" (
	"id" bigserial PRIMARY KEY,
	"qr_code" varchar(255) NOT NULL CONSTRAINT "packages_qr_code_key" UNIQUE,
	"items_count" integer DEFAULT 0 NOT NULL,
	"weight" numeric(10, 3) DEFAULT '0' NOT NULL,
	"amount" numeric(14, 2) DEFAULT '0' NOT NULL,
	"comment" text,
	"created_by" bigint,
	"updated_by" bigint,
	"gladiator_id" bigint,
	"current_step_id" bigint NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
CREATE TABLE "roles" (
	"id" bigserial PRIMARY KEY,
	"code" varchar(50) NOT NULL CONSTRAINT "roles_code_key" UNIQUE,
	"name" varchar(100) NOT NULL CONSTRAINT "roles_name_key" UNIQUE,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
CREATE TABLE "statuses" (
	"id" bigserial PRIMARY KEY,
	"code" varchar(50) NOT NULL CONSTRAINT "statuses_code_key" UNIQUE,
	"name" varchar(100) NOT NULL,
	"type" status_type NOT NULL,
	"created_at" timestamp with time zone DEFAULT now(),
	"updated_at" timestamp with time zone DEFAULT now()
);
CREATE TABLE "user_sessions" (
	"id" bigserial PRIMARY KEY,
	"user_id" bigint NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
CREATE TABLE "users" (
	"id" bigserial PRIMARY KEY,
	"public_code" varchar(50) NOT NULL CONSTRAINT "users_public_code_key" UNIQUE,
	"full_name" varchar(255) NOT NULL,
	"email" varchar(255) NOT NULL CONSTRAINT "users_email_key" UNIQUE,
	"phone" varchar(50),
	"address" text,
	"firebase_uid" varchar(255) CONSTRAINT "users_firebase_uid_key" UNIQUE,
	"role_id" bigint NOT NULL,
	"zone_id" bigint,
	"status" varchar(20) DEFAULT 'ENABLED' NOT NULL,
	"verified_at" timestamp with time zone,
	"last_login_at" timestamp with time zone,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL,
	"deleted_at" timestamp with time zone
);
CREATE TABLE "visas" (
	"id" bigserial PRIMARY KEY,
	"user_id" bigint NOT NULL CONSTRAINT "visas_user_id_key" UNIQUE,
	"visa_status" varchar(20) DEFAULT 'PENDING' NOT NULL,
	"date_from" timestamp with time zone NOT NULL,
	"date_to" timestamp with time zone NOT NULL,
	"number" varchar(50) NOT NULL CONSTRAINT "visas_number_key" UNIQUE,
	"created_by" bigint NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
CREATE TABLE "wallet_transactions" (
	"id" bigserial PRIMARY KEY,
	"wallet_id" bigint NOT NULL,
	"direction" smallint NOT NULL,
	"amount" numeric(14, 2) NOT NULL,
	"user_id" bigint,
	"balance_before" numeric(14, 2) NOT NULL,
	"balance_after" numeric(14, 2) NOT NULL,
	"created_by" bigint,
	"comment" text,
	"status" varchar(20) DEFAULT 'COMPLETED' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	CONSTRAINT "wallet_transactions_direction_check" CHECK ((direction = ANY (ARRAY[1, '-1'::integer])))
);
CREATE TABLE "wallets" (
	"id" bigserial PRIMARY KEY,
	"user_id" bigint NOT NULL CONSTRAINT "wallets_user_id_key" UNIQUE,
	"role_id" bigint NOT NULL,
	"balance" numeric(14, 2) DEFAULT '0' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
CREATE TABLE "zones" (
	"id" bigserial PRIMARY KEY,
	"code" varchar(50) NOT NULL CONSTRAINT "zones_code_key" UNIQUE,
	"name" varchar(100) NOT NULL,
	"zone_type" zone_type NOT NULL,
	"description" text
);
CREATE UNIQUE INDEX "countries_country_key" ON "countries" ("country");
CREATE UNIQUE INDEX "countries_pkey" ON "countries" ("id");
CREATE INDEX "idx_order_item_images_order_item_id" ON "order_item_images" ("order_item_id");
CREATE UNIQUE INDEX "order_item_images_pkey" ON "order_item_images" ("id");
CREATE INDEX "idx_order_item_steps_created_by" ON "order_item_steps" ("created_by");
CREATE INDEX "idx_order_item_steps_item_created" ON "order_item_steps" ("item_id","created_at");
CREATE INDEX "idx_order_item_steps_item_id" ON "order_item_steps" ("item_id");
CREATE INDEX "idx_order_item_steps_status_id" ON "order_item_steps" ("status_id");
CREATE INDEX "idx_order_item_steps_user_id" ON "order_item_steps" ("user_id");
CREATE INDEX "idx_order_item_steps_zone_id" ON "order_item_steps" ("zone_id");
CREATE UNIQUE INDEX "order_item_steps_pkey" ON "order_item_steps" ("id");
CREATE INDEX "idx_order_items_active" ON "order_items" ("id");
CREATE INDEX "idx_order_items_current_step_id" ON "order_items" ("current_step_id");
CREATE INDEX "idx_order_items_order_id" ON "order_items" ("order_id");
CREATE UNIQUE INDEX "order_items_pkey" ON "order_items" ("id");
CREATE UNIQUE INDEX "order_items_public_code_key" ON "order_items" ("public_code");
CREATE INDEX "idx_orders_active" ON "orders" ("id");
CREATE INDEX "idx_orders_client_id" ON "orders" ("client_id");
CREATE UNIQUE INDEX "orders_pkey" ON "orders" ("id");
CREATE INDEX "idx_package_item_receptions_inspected_by" ON "package_item_receptions" ("inspected_by");
CREATE INDEX "idx_package_item_receptions_package_item_id" ON "package_item_receptions" ("package_item_id");
CREATE UNIQUE INDEX "package_item_receptions_pkey" ON "package_item_receptions" ("id");
CREATE INDEX "idx_package_item_steps_created_by" ON "package_item_steps" ("created_by");
CREATE INDEX "idx_package_item_steps_item_created" ON "package_item_steps" ("package_item_id","created_at");
CREATE INDEX "idx_package_item_steps_package_item_id" ON "package_item_steps" ("package_item_id");
CREATE INDEX "idx_package_item_steps_status_id" ON "package_item_steps" ("status_id");
CREATE INDEX "idx_package_item_steps_user_id" ON "package_item_steps" ("user_id");
CREATE INDEX "idx_package_item_steps_zone_id" ON "package_item_steps" ("zone_id");
CREATE UNIQUE INDEX "package_item_steps_pkey" ON "package_item_steps" ("id");
CREATE INDEX "idx_package_items_created_by" ON "package_items" ("created_by");
CREATE INDEX "idx_package_items_current_step_id" ON "package_items" ("current_step_id");
CREATE INDEX "idx_package_items_order_item_id" ON "package_items" ("order_item_id");
CREATE INDEX "idx_package_items_package_id" ON "package_items" ("package_id");
CREATE INDEX "idx_package_items_updated_by" ON "package_items" ("updated_by");
CREATE UNIQUE INDEX "package_items_pkey" ON "package_items" ("id");
CREATE INDEX "idx_package_steps_created_by" ON "package_steps" ("created_by");
CREATE INDEX "idx_package_steps_package_created" ON "package_steps" ("package_id","created_at");
CREATE INDEX "idx_package_steps_package_id" ON "package_steps" ("package_id");
CREATE INDEX "idx_package_steps_status_id" ON "package_steps" ("status_id");
CREATE INDEX "idx_package_steps_user_id" ON "package_steps" ("user_id");
CREATE INDEX "idx_package_steps_zone_id" ON "package_steps" ("zone_id");
CREATE UNIQUE INDEX "package_steps_pkey" ON "package_steps" ("id");
CREATE INDEX "idx_packages_created_by" ON "packages" ("created_by");
CREATE INDEX "idx_packages_current_step_id" ON "packages" ("current_step_id");
CREATE INDEX "idx_packages_gladiator_id" ON "packages" ("gladiator_id");
CREATE INDEX "idx_packages_updated_by" ON "packages" ("updated_by");
CREATE UNIQUE INDEX "packages_pkey" ON "packages" ("id");
CREATE UNIQUE INDEX "packages_qr_code_key" ON "packages" ("qr_code");
CREATE UNIQUE INDEX "roles_code_key" ON "roles" ("code");
CREATE UNIQUE INDEX "roles_name_key" ON "roles" ("name");
CREATE UNIQUE INDEX "roles_pkey" ON "roles" ("id");
CREATE UNIQUE INDEX "statuses_code_key" ON "statuses" ("code");
CREATE UNIQUE INDEX "statuses_pkey" ON "statuses" ("id");
CREATE INDEX "idx_user_sessions_user_id" ON "user_sessions" ("user_id");
CREATE UNIQUE INDEX "user_sessions_pkey" ON "user_sessions" ("id");
CREATE INDEX "idx_users_active" ON "users" ("id");
CREATE INDEX "idx_users_role_id" ON "users" ("role_id");
CREATE INDEX "idx_users_zone_id" ON "users" ("zone_id");
CREATE UNIQUE INDEX "users_email_key" ON "users" ("email");
CREATE UNIQUE INDEX "users_firebase_uid_key" ON "users" ("firebase_uid");
CREATE UNIQUE INDEX "users_pkey" ON "users" ("id");
CREATE UNIQUE INDEX "users_public_code_key" ON "users" ("public_code");
CREATE INDEX "idx_visas_created_by" ON "visas" ("created_by");
CREATE UNIQUE INDEX "visas_number_key" ON "visas" ("number");
CREATE UNIQUE INDEX "visas_pkey" ON "visas" ("id");
CREATE UNIQUE INDEX "visas_user_id_key" ON "visas" ("user_id");
CREATE INDEX "idx_wallet_transactions_created_by" ON "wallet_transactions" ("created_by");
CREATE INDEX "idx_wallet_transactions_user_id" ON "wallet_transactions" ("user_id");
CREATE INDEX "idx_wallet_transactions_wallet_created" ON "wallet_transactions" ("wallet_id","created_at");
CREATE INDEX "idx_wallet_transactions_wallet_id" ON "wallet_transactions" ("wallet_id");
CREATE UNIQUE INDEX "wallet_transactions_pkey" ON "wallet_transactions" ("id");
CREATE INDEX "idx_wallets_role_id" ON "wallets" ("role_id");
CREATE UNIQUE INDEX "wallets_pkey" ON "wallets" ("id");
CREATE UNIQUE INDEX "wallets_user_id_key" ON "wallets" ("user_id");
CREATE UNIQUE INDEX "zones_code_key" ON "zones" ("code");
CREATE UNIQUE INDEX "zones_pkey" ON "zones" ("id");
ALTER TABLE "order_item_images" ADD CONSTRAINT "order_item_images_order_item_id_fkey" FOREIGN KEY ("order_item_id") REFERENCES "order_items"("id") ON DELETE CASCADE;
ALTER TABLE "order_item_steps" ADD CONSTRAINT "order_item_steps_created_by_fkey" FOREIGN KEY ("created_by") REFERENCES "users"("id");
ALTER TABLE "order_item_steps" ADD CONSTRAINT "order_item_steps_item_id_fkey" FOREIGN KEY ("item_id") REFERENCES "order_items"("id") ON DELETE CASCADE;
ALTER TABLE "order_item_steps" ADD CONSTRAINT "order_item_steps_status_id_fkey" FOREIGN KEY ("status_id") REFERENCES "statuses"("id");
ALTER TABLE "order_item_steps" ADD CONSTRAINT "order_item_steps_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id");
ALTER TABLE "order_item_steps" ADD CONSTRAINT "order_item_steps_zone_id_fkey" FOREIGN KEY ("zone_id") REFERENCES "zones"("id");
ALTER TABLE "order_items" ADD CONSTRAINT "order_items_current_step_id_fkey" FOREIGN KEY ("current_step_id") REFERENCES "order_item_steps"("id");
ALTER TABLE "order_items" ADD CONSTRAINT "order_items_order_id_fkey" FOREIGN KEY ("order_id") REFERENCES "orders"("id") ON DELETE CASCADE;
ALTER TABLE "orders" ADD CONSTRAINT "orders_client_id_fkey" FOREIGN KEY ("client_id") REFERENCES "users"("id") ON DELETE RESTRICT;
ALTER TABLE "package_item_receptions" ADD CONSTRAINT "package_item_receptions_inspected_by_fkey" FOREIGN KEY ("inspected_by") REFERENCES "users"("id");
ALTER TABLE "package_item_receptions" ADD CONSTRAINT "package_item_receptions_package_item_id_fkey" FOREIGN KEY ("package_item_id") REFERENCES "package_items"("id") ON DELETE CASCADE;
ALTER TABLE "package_item_steps" ADD CONSTRAINT "package_item_steps_created_by_fkey" FOREIGN KEY ("created_by") REFERENCES "users"("id");
ALTER TABLE "package_item_steps" ADD CONSTRAINT "package_item_steps_package_item_id_fkey" FOREIGN KEY ("package_item_id") REFERENCES "package_items"("id") ON DELETE CASCADE;
ALTER TABLE "package_item_steps" ADD CONSTRAINT "package_item_steps_status_id_fkey" FOREIGN KEY ("status_id") REFERENCES "statuses"("id");
ALTER TABLE "package_item_steps" ADD CONSTRAINT "package_item_steps_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id");
ALTER TABLE "package_item_steps" ADD CONSTRAINT "package_item_steps_zone_id_fkey" FOREIGN KEY ("zone_id") REFERENCES "zones"("id");
ALTER TABLE "package_items" ADD CONSTRAINT "package_items_created_by_fkey" FOREIGN KEY ("created_by") REFERENCES "users"("id");
ALTER TABLE "package_items" ADD CONSTRAINT "package_items_current_step_id_fkey" FOREIGN KEY ("current_step_id") REFERENCES "package_item_steps"("id");
ALTER TABLE "package_items" ADD CONSTRAINT "package_items_order_item_id_fkey" FOREIGN KEY ("order_item_id") REFERENCES "order_items"("id") ON DELETE RESTRICT;
ALTER TABLE "package_items" ADD CONSTRAINT "package_items_package_id_fkey" FOREIGN KEY ("package_id") REFERENCES "packages"("id") ON DELETE CASCADE;
ALTER TABLE "package_items" ADD CONSTRAINT "package_items_updated_by_fkey" FOREIGN KEY ("updated_by") REFERENCES "users"("id");
ALTER TABLE "package_steps" ADD CONSTRAINT "package_steps_created_by_fkey" FOREIGN KEY ("created_by") REFERENCES "users"("id");
ALTER TABLE "package_steps" ADD CONSTRAINT "package_steps_package_id_fkey" FOREIGN KEY ("package_id") REFERENCES "packages"("id") ON DELETE CASCADE;
ALTER TABLE "package_steps" ADD CONSTRAINT "package_steps_status_id_fkey" FOREIGN KEY ("status_id") REFERENCES "statuses"("id");
ALTER TABLE "package_steps" ADD CONSTRAINT "package_steps_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id");
ALTER TABLE "package_steps" ADD CONSTRAINT "package_steps_zone_id_fkey" FOREIGN KEY ("zone_id") REFERENCES "zones"("id");
ALTER TABLE "packages" ADD CONSTRAINT "packages_created_by_fkey" FOREIGN KEY ("created_by") REFERENCES "users"("id");
ALTER TABLE "packages" ADD CONSTRAINT "packages_current_step_id_fkey" FOREIGN KEY ("current_step_id") REFERENCES "package_steps"("id");
ALTER TABLE "packages" ADD CONSTRAINT "packages_gladiator_id_fkey" FOREIGN KEY ("gladiator_id") REFERENCES "users"("id");
ALTER TABLE "packages" ADD CONSTRAINT "packages_updated_by_fkey" FOREIGN KEY ("updated_by") REFERENCES "users"("id");
ALTER TABLE "user_sessions" ADD CONSTRAINT "user_sessions_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id") ON DELETE CASCADE;
ALTER TABLE "users" ADD CONSTRAINT "users_role_id_fkey" FOREIGN KEY ("role_id") REFERENCES "roles"("id");
ALTER TABLE "users" ADD CONSTRAINT "users_zone_id_fkey" FOREIGN KEY ("zone_id") REFERENCES "zones"("id");
ALTER TABLE "visas" ADD CONSTRAINT "visas_created_by_fkey" FOREIGN KEY ("created_by") REFERENCES "users"("id");
ALTER TABLE "visas" ADD CONSTRAINT "visas_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id") ON DELETE CASCADE;
ALTER TABLE "wallet_transactions" ADD CONSTRAINT "wallet_transactions_created_by_fkey" FOREIGN KEY ("created_by") REFERENCES "users"("id");
ALTER TABLE "wallet_transactions" ADD CONSTRAINT "wallet_transactions_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id");
ALTER TABLE "wallet_transactions" ADD CONSTRAINT "wallet_transactions_wallet_id_fkey" FOREIGN KEY ("wallet_id") REFERENCES "wallets"("id") ON DELETE CASCADE;
ALTER TABLE "wallets" ADD CONSTRAINT "wallets_role_id_fkey" FOREIGN KEY ("role_id") REFERENCES "roles"("id");
ALTER TABLE "wallets" ADD CONSTRAINT "wallets_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id") ON DELETE CASCADE;
