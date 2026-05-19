# Guide d'Installation - Thème WordPress MUSICYOS

## 🎵 Bienvenue dans MUSICYOS

MUSICYOS est un thème WordPress complet conçu pour les **compositeurs et producteurs musicaux**. Il vous permet de gérer vos compositions, projets et services tout en offrant une interface moderne et professionnelle.

---

## 📋 Contenu du Thème

```
musicyos/
├── functions.php              # Logique du thème (CPT, taxonomies, shortcodes)
├── style.css                  # Styles principaux + méta-données
├── header.php                 # En-tête du site
├── footer.php                 # Pied de page
├── index.php                  # Page d'accueil
├── archive-composition.php    # Archive des compositions
├── single-composition.php     # Page d'une composition
├── archive-service.php        # Archive des services
├── archive-projet.php         # Archive des projets
├── assets/
│   ├── css/
│   │   └── custom.css        # CSS personnalisé
│   └── js/
│       ├── main.js           # JavaScript principal
│       ├── mixer.js          # Mixeur audio
│       ├── visualizer.js     # Visualiseurs audio
│       └── todo.js           # Application Todo
└── README.md                  # Ce fichier
```

---

## ⚙️ Installation

### 1. **Télécharger le Thème**

```bash
# Via Git
git clone https://github.com/TKIFF2026/MUSIC.git
cd MUSIC/wordpress-theme

# Ou télécharger directement le dossier musicyos/
```

### 2. **Installer sur WordPress**

1. Accédez à votre admin WordPress: `https://votresite.com/wp-admin/`
2. Allez dans **Apparence > Thèmes**
3. Cliquez sur **Ajouter un thème**
4. Téléchargez le dossier `musicyos/` ou utilisez un client FTP
5. Cliquez sur **Activer** une fois téléchargé

### 3. **Configuration Initiale**

Après l'activation du thème:

1. **Personnalisez votre site**:
   - Allez dans **Paramètres > Général**
   - Définissez le titre et la description du site
   - Mettez à jour l'URL

2. **Configurez la Navigation**:
   - Allez dans **Apparence > Menus**
   - Créez un nouveau menu
   - Ajoutez des liens (Accueil, Compositions, Services, Contact, etc.)
   - Assignez-le à **Menu Principal** et **Menu Pied de Page**

3. **Configurez le Logo**:
   - Allez dans **Apparence > Personnaliser**
   - Cliquez sur **Identité du site**
   - Téléchargez votre logo

---

## 🎼 Utiliser les Compositions

### Créer une Nouvelle Composition

1. Allez dans **Compositions > Ajouter une nouvelle**
2. Remplissez les informations:
   - **Titre**: Le nom de votre composition
   - **Description**: Détails et histoire
   - **Image à la une**: Couverture/artwork

3. **Détails de la Composition**:
   - Durée (en secondes)
   - Tempo (BPM)
   - Clé musicale
   - URL du fichier audio

4. **Taxonomies**:
   - **Styles Musicaux**: Électronique, Jazz, etc.
   - **Instruments**: Piano, Guitare, etc.
   - **Genres**: Rock, Pop, etc.

5. Cliquez sur **Publier**

### Afficher les Compositions

Utilisez le shortcode:

```
[musicyos_compositions posts_per_page="6" orderby="date" order="DESC"]
```

---

## 💼 Utiliser les Services

### Créer un Service

1. Allez dans **Services > Ajouter un nouveau**
2. Remplissez:
   - **Titre**: Nom du service
   - **Description**: Détails du service
   - **Prix**: Tarif en euros
   - **Emoji/Icône**: Un emoji représentant le service (🎼, 🎛️, etc.)

3. Cliquez sur **Publier**

### Afficher les Services

Utilisez le shortcode:

```
[musicyos_services posts_per_page="3"]
```

---

## 📁 Utiliser les Projets

### Créer un Projet

1. Allez dans **Projets > Ajouter un nouveau**
2. Remplissez:
   - **Titre**: Nom du projet
   - **Description**: Détails du projet
   - **Client**: Nom du client
   - **Date de fin**: Deadline
   - **Budget**: Budget en euros
   - **Statut**: En cours, Terminé, etc.

3. Cliquez sur **Publier**

---

## 🛠️ Shortcodes Disponibles

### 1. Console de Mixage
```
[musicyos_mixer]
```

### 2. Afficher les Compositions
```
[musicyos_compositions posts_per_page="6" orderby="date" order="DESC"]
```

### 3. Afficher les Services
```
[musicyos_services posts_per_page="3"]
```

### 4. Afficher la Liste de Tâches
```
[musicyos_todo]
```

---

## 📝 Créer des Pages

### Page Accueil
1. Créez une page "Accueil"
2. Utilisez les shortcodes:
   ```
   [musicyos_compositions posts_per_page="3"]
   [musicyos_services posts_per_page="3"]
   [musicyos_mixer]
   ```
3. Allez dans **Paramètres > Lecture**
4. Sélectionnez cette page comme page d'accueil statique

### Page Compositions
1. Créez une page "Compositions"
2. Ajoutez: `[musicyos_compositions posts_per_page="12"]`

### Page Services
1. Créez une page "Services"
2. Ajoutez: `[musicyos_services posts_per_page="6"]`

### Page Tâches
1. Créez une page "Tâches"
2. Ajoutez: `[musicyos_todo]`

---

## 🎨 Personnalisation

### Modifier les Couleurs

Éditez le fichier `style.css`:

```css
:root {
    --primary: #00d4ff;      /* Cyan */
    --secondary: #ff006e;    /* Rose */
    --accent: #8338ec;       /* Violet */
    --bg-dark: #0a0e27;      /* Fond sombre */
}
```

### Modifier les Polices

Dans `functions.php`, ajoutez:

```php
function musicyos_google_fonts() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
}
add_action('wp_enqueue_scripts', 'musicyos_google_fonts');
```

### Ajouter du CSS Personnalisé

1. Allez dans **Apparence > Personnaliser**
2. Cliquez sur **CSS Personnalisé**
3. Ajoutez vos styles

---

## 🔌 Plugins Recommandés

Pour une fonctionnalité optimale, installez ces plugins:

1. **Contact Form 7** - Créer des formulaires
2. **All in One SEO** - Optimisation SEO
3. **Jetpack** - Performance et sécurité
4. **Wordfence** - Sécurité
5. **WP Super Cache** - Mise en cache
6. **UpdraftPlus** - Sauvegardes

---

## 📞 Support et Contact

Pour des questions ou besoin d'assistance:

- **GitHub**: https://github.com/TKIFF2026/MUSIC
- **Email**: kiffeurzik0101@gmail.com

---

## 📜 Licence

MUSICYOS est sous licence **GPL v2 ou ultérieure**. Vous êtes libre de modifier et redistribuer le thème selon les termes de la licence.

---

## 🎯 Prochaines Étapes

1. **Installez le thème** sur votre WordPress
2. **Configurez les menus** et l'identité du site
3. **Créez vos compositions** et services
4. **Personnalisez les couleurs** selon votre marque
5. **Installez les plugins recommandés**
6. **Testez tous les shortcodes**
7. **Optimisez le SEO**

---

**Créé avec ❤️ par TKIFF2026**

Bienvenue dans MUSICYOS - Votre plateforme de composition musicale sur WordPress! 🎵
