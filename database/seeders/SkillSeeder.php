<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            'it' => ['HTML', 'CSS', 'JavaScript', 'TypeScript', 'Vue.js', 'React', 'Angular', 'PHP', 'Laravel', 'Symfony',
'Blade', 'Livewire', 'Tailwind', 'Bootstrap', 'JSON', 'REST', 'AJAX', 'Inertia.js', 'SQL', 'MySQL',
'PostgreSQL', 'MongoDB', 'Git', 'GitHub', 'GitLab', 'JQuery', 'Node.js', 'Express.js', 'GraphQL', 'API RESTful',
'Webpack', 'Babel', 'Docker', 'Kubernetes', 'CI/CD', 'Jenkins', 'Vagrant', 'Scrum', 'Agile', 'Java', 'JavaFX', 'Kotlin', 'Android', 'SQLite', 'Room', 'XML', 'Firebase', 'C#', '.NET',
'Visual Studio', 'UML', 'Swing', 'IntelliJ', 'Maven', 'Gradle', 'JUnit', 'Dart', 'Flutter', 'Git',
'Swift', 'Xcode', 'Objective-C', 'Ruby', 'C++', 'Android Studio', 'MySQL', 'PostgreSQL', 'Docker', 'Linux',
'React Native', 'HTML5', 'CSS3', 'JavaScript', 'TypeScript', 'SQL', 'Node.js', 'MongoDB', 'jQuery', 'GitLab', 'Linux', 'Ubuntu', 'Bash', 'Windows Server', 'Active Directory', 'DNS', 'DHCP', 'pfSense', 'Firewall', 'Apache',
'Nginx', 'Proxmox', 'VMware', 'VirtualBox', 'Samba', 'Zabbix', 'Nagios', 'Ansible', 'LDAP', 'SSH',
'IPTables', 'Cloud Computing', 'AWS', 'Google Cloud', 'Docker', 'Kubernetes', 'CI/CD', 'Git', 'Network Security', 'VPN',
'Monitoring', 'Backup', 'Rsync', 'NTP', 'Zabbix', 'GlusterFS', 'ELK Stack', 'Grafana', 'Prometheus', 'F5 Big-IP'


],
           
            'marketing' => ['SEO', 'SEM', 'Google Ads', 'Meta Ads', 'Marketing de contenidos', 'Copywriting', 'Email Marketing', 'Automatización de marketing', 'Inbound Marketing', 'Growth Hacking',
'Google Analytics', 'Google Tag Manager', 'Data Studio', 'Marketing Digital', 'Publicidad Programática', 'Marketing en buscadores', 'Marketing en redes sociales', 'Estrategia de marca', 'Plan de marketing', 'Funnel de conversión',
'Social Media', 'Instagram Ads', 'LinkedIn Ads', 'TikTok Ads', 'Community Management', 'Gestión de campañas', 'Publicidad digital', 'Buyer Persona', 'CTR', 'CPC',
'Lead Nurturing', 'CRM', 'HubSpot', 'Mailchimp', 'Hootsuite', 'Metricool', 'Canva', 'Photoshop', 'Video Marketing', 'WordPress',
'Optimización de landing pages', 'A/B Testing', 'UX Writing', 'Neuromarketing', 'Estrategia de contenidos', 'Análisis de competencia', 'E-commerce', 'Storytelling', 'Branding', 'Reputación online'
], 'automotive' => ['Diagnóstico de averías', 'Mecánica automotriz', 'Motores de combustión interna', 'Sistemas eléctricos automotrices', 'Sistemas de suspensión', 'Sistemas de frenos', 'Transmisión', 'Dirección', 'Carrocería', 'Aire acondicionado',
'Frenos ABS', 'Sistemas de escape', 'Fugas de refrigerante', 'Reparación de motores', 'Lubricación de motores', 'Sistemas de alimentación de combustible', 'Inyección electrónica', 'Turbocompresores', 'Motores diésel', 'Motores híbridos',
'Chasis', 'Ruedas y neumáticos', 'Diagnóstico con escáner OBD', 'Eje de transmisión', 'Sistema de suspensión neumática', 'Sistemas de dirección asistida', 'Alineación de ruedas', 'Ajuste de válvulas', 'Reemplazo de correas de distribución', 'Sistemas de embrague',
'Instalación de componentes mecánicos', 'Soldadura automotriz', 'Técnicas de enderezado de carrocerías', 'Inspección y mantenimiento de frenos', 'Reemplazo de amortiguadores', 'Sistema de suspensión independiente', 'Reparación de sistemas eléctricos', 'Reparación de alternadores', 'Ajuste de frenos de disco', 'Ajuste de frenos de tambor',
'Sistemas de arranque', 'Pruebas de compresión del motor', 'Bancadas de motor', 'Balanceo de ruedas', 'Reemplazo de bujías', 'Sistemas de refrigeración', 'Correas de transmisión', 'Sistemas de combustible diésel', 'Filtros de aire', 'Lubricantes',
'Reparación de sistemas de climatización', 'Reparación de radiadores', 'Inspección de frenos de aire', 'Técnicas de corte y soldadura', 'Mantenimiento preventivo', 'Pruebas de fugas', 'Escaneo y diagnóstico electrónico', 'Instalación de sistemas de escape', 'Técnicas de reparación de la carrocería', 'Reparación de sistemas de dirección',
'Sistemas de suspensión hidráulica', 'Reemplazo de cables de encendido', 'Técnicas de alineación de motores', 'Técnicas de reparación de embragues', 'Reparación de bombas de combustible', 'Reparación de inyectores', 'Sistema de control de emisiones', 'Sistemas de válvulas', 'Reparación de cajas de cambios', 'Inspección de componentes mecánicos',
'Mantenimiento de vehículos industriales', 'Mantenimiento de vehículos pesados', 'Sistemas electrónicos de automóviles', 'Sistemas de transmisión automática', 'Control de emisiones en motores', 'Tecnología de vehículos eléctricos', 'Sistema de frenos de aire', 'Mantenimiento de sistemas hidráulicos', 'Frenos de disco ventilados', 'Sistemas de válvulas variable'
]
        ];

        foreach ($skills as $category => $skillList) {
            foreach ($skillList as $skillName) {
                Skill::firstOrCreate([
                    'name' => $skillName,
                    'category' => $category
                ]);
            }
        }
    }
}
