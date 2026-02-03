/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     17/1/2026 0:50:44                            */
/*==============================================================*/


/*==============================================================*/
/* Table: EDIFICIOS                                             */
/*==============================================================*/
create table EDIFICIOS
(
   CODIGO_EDIFICIOS     varchar(100) not null,
   NOMBRE_EDIFICIO      varchar(100),
   DIRECCION_EDIFICIO   text,
   primary key (CODIGO_EDIFICIOS)
);

/*==============================================================*/
/* Table: ESPECIALIDAD                                          */
/*==============================================================*/
create table ESPECIALIDAD
(
   CODIGO_ESPECIALIDAD  varchar(100) not null,
   NOMBRE_ESPECIALIDAD  varchar(100),
   primary key (CODIGO_ESPECIALIDAD)
);

/*==============================================================*/
/* Table: ESPECIALIDAD_PROFESOR                                 */
/*==============================================================*/
create table ESPECIALIDAD_PROFESOR
(
   CODIGO_ESPECIALIDAD  varchar(100) not null,
   CODIGO_PROFESOR      varchar(100) not null,
   primary key (CODIGO_ESPECIALIDAD, CODIGO_PROFESOR)
);

/*==============================================================*/
/* Table: ESTUDIANTES                                           */
/*==============================================================*/
create table ESTUDIANTES
(
   CODIGO_ESTUDIANTE           varchar(100) not null,
   NOMBRE_ESTUDIANTE           varchar(100),
   APELLIDO_ESTUDIANTE         varchar(100),
   FECHA_NACIMIENTO            date,
   DIRECCION_ESTUDIANTE        text,
   CORREO_ESTUDIANTE           text,
   primary key (CODIGO_ESTUDIANTE)
);

/*==============================================================*/
/* Table: HORARIOS                                              */
/*==============================================================*/
create table HORARIOS
(
   CODIGO_HORARIO       varchar(100) not null,
   CODIGO_MATERIA       varchar(100) not null,
   DIA                  varchar(100),
   HORA                 time,
   DURACION             time,
   primary key (CODIGO_HORARIO)
);

/*==============================================================*/
/* Table: MATERIAS                                              */
/*==============================================================*/
create table MATERIAS
(
   CODIGO_MATERIA       varchar(100) not null,
   CODIGO_ESPECIALIDAD  varchar(100) not null,
   CODIGO_EDIFICIOS     varchar(100) not null,
   NOMBRE_MATERIA       varchar(100),
   DESCRIPCION          text,
   primary key (CODIGO_MATERIA)
);

/*==============================================================*/
/* Table: MATRICULA                                             */
/*==============================================================*/
create table MATRICULA
(
   CODIGO_MATRICULA     varchar(100) not null,
   CODIGO_ESTUDIANTE    varchar(100) not null,
   FECHA_MATRICULA      date,
   primary key (CODIGO_MATRICULA)
);

/*==============================================================*/
/* Table: MATRICULA_MATERIA                                     */
/*==============================================================*/
create table MATRICULA_MATERIA
(
   CODIGO_MATERIA       varchar(100) not null,
   CODIGO_MATRICULA     varchar(100) not null,
   primary key (CODIGO_MATERIA, CODIGO_MATRICULA)
);

/*==============================================================*/
/* Table: PROFESORES                                            */
/*==============================================================*/
create table PROFESORES
(
   CODIGO_PROFESOR      varchar(100) not null,
   APELLIDO_PROFESOR    varchar(100),
   CORREO_PROFESOR      text,
   NOMBRE_PROFESOR      varchar(100),
   primary key (CODIGO_PROFESOR)
);

alter table ESPECIALIDAD_PROFESOR add constraint FK_RELATIONSHIP_1 foreign key (CODIGO_ESPECIALIDAD)
      references ESPECIALIDAD (CODIGO_ESPECIALIDAD) on delete restrict on update restrict;

alter table ESPECIALIDAD_PROFESOR add constraint FK_RELATIONSHIP_7 foreign key (CODIGO_PROFESOR)
      references PROFESORES (CODIGO_PROFESOR) on delete restrict on update restrict;

alter table HORARIOS add constraint FK_RELATIONSHIP_5 foreign key (CODIGO_MATERIA)
      references MATERIAS (CODIGO_MATERIA) on delete restrict on update restrict;

alter table MATERIAS add constraint FK_RELATIONSHIP_2 foreign key (CODIGO_ESPECIALIDAD)
      references ESPECIALIDAD (CODIGO_ESPECIALIDAD) on delete restrict on update restrict;

alter table MATERIAS add constraint FK_RELATIONSHIP_6 foreign key (CODIGO_EDIFICIOS)
      references EDIFICIOS (CODIGO_EDIFICIOS) on delete restrict on update restrict;

alter table MATRICULA add constraint FK_RELATIONSHIP_3 foreign key (CODIGO_ESTUDIANTE)
      references ESTUDIANTES (CODIGO_ESTUDIANTE) on delete restrict on update restrict;

alter table MATRICULA_MATERIA add constraint FK_RELATIONSHIP_4 foreign key (CODIGO_MATERIA)
      references MATERIAS (CODIGO_MATERIA) on delete restrict on update restrict;

alter table MATRICULA_MATERIA add constraint FK_RELATIONSHIP_8 foreign key (CODIGO_MATRICULA)
      references MATRICULA (CODIGO_MATRICULA) on delete restrict on update restrict;

