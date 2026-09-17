<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú de Licores - BARTEK</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: radial-gradient(circle at 50% 20%, #1a0a02 0%, #050201 60%, #000000 100%);
            font-family: 'Outfit', sans-serif;
            display: flex; 
            flex-direction: column;
            align-items: center; 
            justify-content: center;
            padding: 30px 20px; 
            color: #e2e8f0;
        }

        /* CONTENEDOR GENERAL PARA LOS DOS CUADROS */
        .contenedor-principal {
            width: 780px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* CUADRO 1: BIENVENIDA SUPERIOR */
        .tarjeta-bienvenida {
            background: linear-gradient(135deg, #120501 0%, #080808 100%);
            border: 1px solid rgba(224, 102, 20, 0.4);
            box-shadow: 0 0 30px rgba(224, 102, 20, 0.12);
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            position: relative;
        }
        .tarjeta-bienvenida::before {
            content: ""; position: absolute; inset: 6px;
            border: 1px solid rgba(224, 102, 20, 0.15); pointer-events: none; border-radius: 8px;
        }
        .bienvenida-sub {
            font-family: 'Cinzel', serif;
            font-size: 10px;
            letter-spacing: 5px;
            color: #e06614;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .bienvenida-titulo {
            font-family: 'Cinzel', serif;
            font-size: 38px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 4px;
            margin-bottom: 6px;
            text-shadow: 0 0 15px rgba(224, 102, 20, 0.4);
        }
        .bienvenida-badge {
            display: inline-block;
            color: #9ca3af;
            font-size: 8.5px;
            letter-spacing: 3px;
            text-transform: uppercase;
            border: 1px solid rgba(224, 102, 20, 0.3);
            background: rgba(0,0,0,0.5);
            padding: 4px 12px;
            border-radius: 20px;
        }

        /* CUADRO 2: LA CARTA (TU CÓDIGO ORIGINAL) */
        .carta {
            width: 100%; 
            min-height: 940px;
            position: relative; 
            background: #080808;
            border: 1px solid rgba(224, 102, 20, 0.4);
            box-shadow: 0 0 50px rgba(224, 102, 20, 0.15), inset 0 0 30px rgba(0, 0, 0, 0.9);
            padding: 30px 35px 35px; 
            border-radius: 12px;
        }
        .carta::before {
            content: ""; position: absolute; inset: 8px;
            border: 1px solid rgba(224, 102, 20, 0.15); pointer-events: none; border-radius: 8px;
        }
        .encabezado {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            border-bottom: 1px solid #2a2a2a;
            padding-bottom: 20px;
            margin-bottom: 25px;
            position: relative;
            z-index: 2;
        }
        .logo { display: flex; align-items: center; gap: 12px; }
        .logo-b { font-family: 'Cinzel', serif; font-size: 65px; font-weight: 900; color: #e06614; line-height: 1; text-shadow: 0 0 15px rgba(224, 102, 20, 0.4); }
        .logo-info { display: flex; flex-direction: column; }
        .logo-nombre { font-family: 'Cinzel', serif; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: 3px; }
        .logo-sub { color: #9ca3af; font-size: 8px; letter-spacing: 4px; margin-top: 4px; text-transform: uppercase; }
        
        .encabezado-linea {
            width: 1px;
            height: 45px;
            background: linear-gradient(to bottom, rgba(224,102,20,0), rgba(224,102,20,0.6), rgba(224,102,20,0));
            margin: 0 auto;
        }

        .titulo { text-align: right; }
        .titulo h1 { font-family: 'Cinzel', serif; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: 2px; }
        .titulo p { color: #e06614; font-size: 9px; letter-spacing: 2px; margin-top: 6px; font-weight: 600; text-transform: uppercase; }
        
        .columnas { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; position: relative; z-index: 2; margin-bottom: 15px; }
        .columna-derecha { border-left: 1px solid #222; padding-left: 25px; display: flex; flex-direction: column; justify-content: space-between; }
        .columna-izquierda { display: flex; flex-direction: column; gap: 12px; }
        
        .categoria { margin-bottom: 10px; }
        .categoria-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 3px solid #e06614;
            padding-left: 8px;
            margin-bottom: 6px;
        }
        .categoria h2 {
            display: flex; align-items: center; gap: 8px; color: #e06614;
            font-family: 'Cinzel', serif; font-size: 14px; font-weight: 700; letter-spacing: 1.5px;
        }
        .puntos-badge {
            font-size: 8.5px;
            color: #ffa233;
            background: rgba(224, 102, 20, 0.1);
            border: 1px solid rgba(224, 102, 20, 0.25);
            padding: 1px 5px;
            border-radius: 4px;
            font-weight: 600;
        }

        .producto { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 5px; font-size: 12px; position: relative; }
        .producto-nombre { color: #d1d5db; background-color: #080808; padding-right: 6px; z-index: 2; font-weight: 300; }
        .precio { color: #ffa233; background-color: #080808; padding-left: 6px; z-index: 2; font-weight: 700; }
        .producto::after {
            content: ""; position: absolute; bottom: 3px; left: 0; width: 100%; height: 1px;
            background-image: linear-gradient(to right, #333 40%, rgba(255,255,255,0) 0%);
            background-size: 4px 1px; background-repeat: repeat-x; z-index: 1;
        }

        /* Banner elegante para rellenar el espacio vacío abajo a la derecha */
        .banner-bar {
            margin-top: 15px;
            height: 100px;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.85)), url('https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=600&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            border: 1px solid rgba(224, 102, 20, 0.3);
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 10px;
            position: relative;
        }
        .banner-bar h4 {
            font-family: 'Cinzel', serif;
            font-size: 12px;
            color: #ffffff;
            letter-spacing: 2px;
            margin-bottom: 3px;
        }
        .banner-bar p {
            font-size: 8.5px;
            color: #ffa233;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Tabla de Fidelización */
        .fidelizacion {
            margin-top: 15px; padding: 12px 15px; border: 1px solid rgba(224, 102, 20, 0.4);
            background: linear-gradient(135deg, #0f0502 0%, #030303 100%); border-radius: 8px; text-align: center; position: relative; z-index: 2;
            box-shadow: 0 0 15px rgba(224, 102, 20, 0.08);
        }
        .fidelizacion-titulo { font-family: 'Cinzel', serif; font-size: 10.5px; font-weight: 700; color: #ffffff; letter-spacing: 1.5px; margin-bottom: 2px; }
        .fidelizacion-sub { color: #e06614; font-size: 8.5px; letter-spacing: 2px; font-weight: 700; margin-bottom: 8px; text-transform: uppercase; }
        .puntos-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 8px; }
        .punto-item { background: rgba(0, 0, 0, 0.6); border: 1px solid #261205; padding: 5px; border-radius: 6px; font-size: 9.5px; }
        .punto-item span { display: block; color: #9ca3af; font-size: 7.5px; margin-bottom: 1px; }
        .punto-item strong { color: #ffa233; font-size: 10.5px; font-weight: 700; }
        .canje { font-size: 9.5px; color: #d1d5db; background: rgba(224, 102, 20, 0.08); padding: 5px 10px; border-radius: 5px; border: 1px dashed rgba(224, 102, 20, 0.3); display: inline-block; }
        .canje strong { color: #ffffff; }

        .pie { text-align: center; color: #9ca3af; font-size: 9px; letter-spacing: 1.5px; margin-top: 12px; border-top: 1px solid #222; padding-top: 10px; text-transform: uppercase; position: relative; z-index: 2; }
        .ubicacion { color: #e06614; font-weight: bold; }
        .responsabilidad { display: block; color: #6b7280; font-size: 7.5px; letter-spacing: 1px; margin-top: 2px; }
        
        @media(max-width: 800px) {
            .contenedor-principal { width: 100%; }
            .carta { width: 100%; padding: 20px; }
            .encabezado { grid-template-columns: 1fr; gap: 15px; text-align: center; }
            .encabezado-linea { display: none; }
            .titulo { text-align: center; }
            .logo { justify-content: center; }
            .columnas { grid-template-columns: 1fr; gap: 20px; }
            .columna-derecha { border-left: none; padding-left: 0; border-top: 1px solid #222; padding-top: 20px; }
            .banner-bar { display: none; }
        }
    </style>
</head>
<body>

<div class="contenedor-principal">

    <!-- CUADRO 1: BIENVENIDA -->
    <div class="tarjeta-bienvenida">
        <div class="bienvenida-sub">Bienvenidos a</div>
        <div class="bienvenida-titulo">BARTEK</div>
        <div class="bienvenida-badge"><i class="fa-solid fa-qrcode"></i> Menú Digital Oficial</div>
    </div>

    <!-- CUADRO 2: CARTA DE LICORES -->
    <div class="carta">
        <div class="encabezado">
            <div class="logo">
                <div class="logo-b">B</div>
                <div class="logo-info">
                    <div class="logo-nombre">BARTEK</div>
                    <div class="logo-sub">BAR &amp; LOUNGE</div>
                </div>
            </div>
            
            <div class="encabezado-linea"></div>

            <div class="titulo">
                <h1>MENÚ DE LICORES</h1>
                <p>CALIDAD, SABOR Y EL MEJOR AMBIENTE</p>
            </div>
        </div>

        <?php
        $menu = [
            'izq' => [
                'WHISKY' => ['icon' => 'fa-glass-whiskey', 'pts' => '+15 Pts', 'items' => [['Johnnie Walker Black Label', 180000], ['Old Parr 12 Años', 195000], ["Buchanan's 12 Años", 210000], ['Chivas Regal 12 Años', 200000], ["Jack Daniel's", 160000]]],
                'RON' => ['icon' => 'fa-wine-bottle', 'pts' => '+10 Pts', 'items' => [['Ron Medellín Añejo', 95000], ['Ron Viejo de Caldas', 110000], ['Havana Club 3 Años', 140000], ['Bacardí Carta Blanca', 115000], ['Zacapa 23', 260000]]],
                'VODKA' => ['icon' => 'fa-martini-glass', 'pts' => '+10 Pts', 'items' => [['Smirnoff', 95000], ['Absolut', 145000], ['Grey Goose', 280000], ['Stolichnaya', 135000], ['Belvedere', 260000]]],
                'GINEBRA' => ['icon' => 'fa-flask', 'pts' => '+10 Pts', 'items' => [['Bombay Sapphire', 185000], ['Tanqueray', 170000], ['Beefeater', 130000], ["Hendrick's", 220000]]]
            ],
            'der' => [
                'TEQUILA' => ['icon' => 'fa-champagne-glasses', 'pts' => '+15 Pts', 'items' => [['José Cuervo Especial', 140000], ['Don Julio Blanco', 330000], ['Don Julio Reposado', 360000], ['1800 Añejo', 220000], ['Patrón Silver', 350000]]],
                'CERVEZA' => ['icon' => 'fa-beer-mug-empty', 'pts' => '+5 Pts', 'items' => [['Corona', 12000], ['Heineken', 10000], ['Club Colombia', 9000], ['Budweiser', 9000], ['Stella Artois', 11000], ['Águila', 8000]]],
                'CÓCTELES' => ['icon' => 'fa-cocktail', 'pts' => '+5 Pts', 'items' => [['Mojito', 28000], ['Margarita', 30000], ['Daiquiri', 29000], ['Sex on the Beach', 32000], ['Piña Colada', 30000], ['Bloody Mary', 28000], ['Gin Tonic', 26000], ['Whisky Sour', 28000]]]
            ]
        ];
        ?>

        <div class="columnas">
            <!-- Columna Izquierda -->
            <div class="columna-izquierda">
                <?php foreach ($menu['izq'] as $cat => $data): ?>
                    <div class="categoria">
                        <div class="categoria-header">
                            <h2><i class="fa-solid <?php echo $data['icon']; ?>"></i> <?php echo $cat; ?></h2>
                            <span class="puntos-badge"><i class="fa-solid fa-star"></i> <?php echo $data['pts']; ?></span>
                        </div>
                        <?php foreach ($data['items'] as $prod): ?>
                            <div class="producto">
                                <span class="producto-nombre"><?php echo $prod[0]; ?></span>
                                <span class="precio">$<?php echo number_format($prod[1], 0, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Columna Derecha -->
            <div class="columna-derecha">
                <div>
                    <?php foreach ($menu['der'] as $cat => $data): ?>
                        <div class="categoria">
                            <div class="categoria-header">
                                <h2><i class="fa-solid <?php echo $data['icon']; ?>"></i> <?php echo $cat; ?></h2>
                                <span class="puntos-badge"><i class="fa-solid fa-star"></i> <?php echo $data['pts']; ?></span>
                            </div>
                            <?php foreach ($data['items'] as $prod): ?>
                                <div class="producto">
                                    <span class="producto-nombre"><?php echo $prod[0]; ?></span>
                                    <span class="precio">$<?php echo number_format($prod[1], 0, ',', '.'); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Banner elegante para rellenar el espacio vacío abajo a la derecha -->
                <div class="banner-bar">
                    <h4>EXPERIENCIA EXCLUSIVA</h4>
                    <p>LA MEJOR COCTELERÍA DE LA CIUDAD</p>
                </div>
            </div>
        </div>

        <!-- TABLA DE FIDELIZACIÓN -->
        <div class="fidelizacion">
            <div class="fidelizacion-titulo">CLUB DE FIDELIZACIÓN</div>
            <div class="fidelizacion-sub">¡ACUMULA PUNTOS CON TU CONSUMO!</div>
            
            <div class="puntos-grid">
                <div class="punto-item">
                    <span>Cervezas y Cocteles</span>
                    <strong>+5 Puntos</strong>
                </div>
                <div class="punto-item">
                    <span>Ron, Vodka, Ginebra</span>
                    <strong>+10 Puntos</strong>
                </div>
                <div class="punto-item">
                    <span>Whisky y Tequila</span>
                    <strong>+15 Puntos</strong>
                </div>
            </div>

            <div class="canje">
                ¡Canjea tus puntos por <strong>Cerveza Gratis</strong> o <strong>Rebajas en Whisky</strong> con tu mesero!
            </div>

            <a href="<?php echo BASE_URL; ?>/puntos/registro" class="btn-registro-puntos" style="display:inline-block; margin-top:10px; padding:8px 18px; border-radius:20px; background:linear-gradient(135deg,#e06614,#ffa233); color:#0a0a0a; font-weight:700; font-size:10.5px; letter-spacing:1px; text-decoration:none; text-transform:uppercase;">
                <i class="fa-solid fa-user-plus"></i> Regístrate y acumula puntos
            </a>
        </div>

        <!-- PIE DE CARTA -->
        <div class="pie">
            <span class="ubicacion">📍 BARTEK</span> — DONDE CADA NOCHE TIENE UNA HISTORIA.
            <span class="responsabilidad">DISFRUTA CON RESPONSABILIDAD</span>
        </div>
    </div>

</div>

</body>
</html>