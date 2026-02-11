/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     3/2/2026 22:31:27                            */
/*==============================================================*/


drop table if exists CATEGORIA;

drop table if exists CLIENTE;

drop table if exists DETALLE_VENTA;

drop table if exists EMPLEADO;

drop table if exists PRODUCTO;

drop table if exists PROVEEDOR;

drop table if exists TALLA;

drop table if exists VENTAS;

/*==============================================================*/
/* Table: CATEGORIA                                             */
/*==============================================================*/
create table CATEGORIA
(
   CATEGORIA_ID         int not null auto_increment,
   CODIGO               varchar(50),
   DESCRIPCION          varchar(200),
   primary key (CATEGORIA_ID)
);

/*==============================================================*/
/* Table: CLIENTE                                               */
/*==============================================================*/
create table CLIENTE
(
   CLIENTE_ID           int not null auto_increment,
   NOMBRE               varchar(100),
   APELLIDO             varchar(50),
   TELEFONO             varchar(15),
   EMAIL                varchar(100),
   DIRECCION            varchar(300),
   primary key (CLIENTE_ID)
);

/*==============================================================*/
/* Table: DETALLE_VENTA                                         */
/*==============================================================*/
create table DETALLE_VENTA
(
   DETALLE_ID           int not null auto_increment,
   VENTA_ID             int not null,
   PRODUCTO_ID          int not null,
   CANTIDAD             int,
   PRECIO               decimal(10,2),
   primary key (DETALLE_ID)
);

/*==============================================================*/
/* Table: EMPLEADO                                              */
/*==============================================================*/
create table EMPLEADO
(
   EMPLEADO_ID          int not null auto_increment,
   NOMBRE               varchar(100),
   APELLIDO             varchar(50),
   CARGO                varchar(50),
   TELEFONO             varchar(15),
   DIRECCION            varchar(300),
   FECHA_INGRESO        timestamp,
   primary key (EMPLEADO_ID)
);

/*==============================================================*/
/* Table: PRODUCTO                                              */
/*==============================================================*/
create table PRODUCTO
(
   PRODUCTO_ID          int not null auto_increment,
   CATEGORIA_ID         int not null,
   PROVEEDOR_ID         int not null,
   TALLA_ID             int not null,
   CODIGO               varchar(50),
   DESCRIPCION          varchar(200),
   COLOR                varchar(10),
   MARCA                varchar(30),
   STOCK                int,
   PRECIO               decimal(10,2),
   primary key (PRODUCTO_ID)
);

/*==============================================================*/
/* Table: PROVEEDOR                                             */
/*==============================================================*/
create table PROVEEDOR
(
   PROVEEDOR_ID         int not null auto_increment,
   NOMBRE_EMPRESA       varchar(50),
   TELEFONO             varchar(15),
   EMAIL                varchar(100),
   DIRECCION            varchar(300),
   CIUDAD               varchar(50),
   primary key (PROVEEDOR_ID)
);

/*==============================================================*/
/* Table: TALLA                                                 */
/*==============================================================*/
create table TALLA
(
   TALLA_ID             int not null auto_increment,
   CODIGO               varchar(50),
   DESCRIPCION          varchar(200),
   primary key (TALLA_ID)
);

/*==============================================================*/
/* Table: VENTAS                                                */
/*==============================================================*/
create table VENTAS
(
   VENTA_ID             int not null auto_increment,
   CLIENTE_ID           int not null,
   EMPLEADO_ID          int not null,
   FECHA                datetime,
   TOTAL                decimal(10,2),
   ESTADO               varchar(100),
   METODO_PAGO          varchar(100),
   primary key (VENTA_ID)
);

alter table DETALLE_VENTA add constraint FK_DETALLE_VENTA foreign key (VENTA_ID)
      references VENTAS (VENTA_ID) on delete restrict on update restrict;

alter table DETALLE_VENTA add constraint FK_DETALLE_VENTA2 foreign key (PRODUCTO_ID)
      references PRODUCTO (PRODUCTO_ID) on delete restrict on update restrict;

alter table PRODUCTO add constraint FK_CATEGORIA_PRODUCTO foreign key (CATEGORIA_ID)
      references CATEGORIA (CATEGORIA_ID) on delete restrict on update restrict;

alter table PRODUCTO add constraint FK_PROVEEDOR_PRODUCTO foreign key (PROVEEDOR_ID)
      references PROVEEDOR (PROVEEDOR_ID) on delete restrict on update restrict;

alter table PRODUCTO add constraint FK_TALLA_PRODUCTO foreign key (TALLA_ID)
      references TALLA (TALLA_ID) on delete restrict on update restrict;

alter table VENTAS add constraint FK_CLIENTE_VENTAS foreign key (CLIENTE_ID)
      references CLIENTE (CLIENTE_ID) on delete restrict on update restrict;

alter table VENTAS add constraint FK_EMPLEADO_VENTA foreign key (EMPLEADO_ID)
      references EMPLEADO (EMPLEADO_ID) on delete restrict on update restrict;
