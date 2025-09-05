CREATE TABLE `imports` (
  `import_id` int(11) NOT NULL,
  `import_ref` varchar(38) NOT NULL,
  `import_state` enum('pending','finished') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `imports`
--
ALTER TABLE `imports`
  ADD PRIMARY KEY (`import_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `imports`
--
ALTER TABLE `imports`
  MODIFY `import_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;