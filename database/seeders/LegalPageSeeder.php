<?php

namespace Database\Seeders;

use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class LegalPageSeeder extends Seeder
{
    public function run(): void
    {
        LegalPage::updateOrCreate(
            ['type' => 'privacy_policy'],
            [
                'title' => 'Privacy Policy',
                'content' => <<<'HTML'
<h2>1. Introduction</h2>
<p>Welcome to Dinamika Publika. We respect your privacy and are committed to protecting your personal data.</p>

<h2>2. Information We Collect</h2>
<p>We may collect the following types of information:</p>
<ul>
    <li><strong>Identity Data</strong> — such as your name and other identifying information.</li>
    <li><strong>Contact Data</strong> — such as your email address and contact details.</li>
    <li><strong>Technical Data</strong> — such as IP address, browser type, and device information.</li>
    <li><strong>Usage Data</strong> — information about how you use our website.</li>
    <li><strong>Marketing and Communications Data</strong> — your preferences regarding communications and marketing.</li>
</ul>

<h2>3. How We Use Your Information</h2>
<p>We may use your information to:</p>
<ul>
    <li>Register and manage your user account.</li>
    <li>Manage our relationship with you.</li>
    <li>Deliver relevant website content and advertisements.</li>
    <li>Perform analytics and improve our services.</li>
    <li>Provide relevant recommendations.</li>
</ul>

<h2>4. Cookies</h2>
<p>Our website may use cookies for the following purposes:</p>
<ul>
    <li><strong>Strictly necessary cookies</strong></li>
    <li><strong>Analytical and performance cookies</strong></li>
    <li><strong>Functionality cookies</strong></li>
</ul>

<h2>5. Data Security</h2>
<p>We take reasonable measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction.</p>

<h2>6. Data Retention</h2>
<p>We retain personal information only for as long as necessary to fulfill the purposes for which it was collected, unless a longer retention period is required by law.</p>

<h2>7. Your Legal Rights</h2>
<p>Depending on applicable law, you may have the right to:</p>
<ul>
    <li>Access your personal data.</li>
    <li>Request correction of inaccurate data.</li>
    <li>Request erasure of your data.</li>
    <li>Object to certain processing activities.</li>
    <li>Request restriction of processing.</li>
    <li>Request transfer of your data.</li>
    <li>Withdraw consent where processing is based on consent.</li>
</ul>

<h2>8. Third-Party Links</h2>
<p>Our website may contain links to third-party websites. We are not responsible for the privacy practices or content of those external websites.</p>

<h2>9. Children's Privacy</h2>
<p>Our services are not intended for children under the age of 13.</p>

<h2>10. Changes to This Privacy Policy</h2>
<p>We may update this Privacy Policy from time to time. Any changes will be reflected on this page.</p>

<h2>11. Contact Us</h2>
<p>If you have questions about this Privacy Policy, you can contact us through the information provided on our website.</p>
HTML,
                'published_at' => now(),
            ]
        );

        LegalPage::updateOrCreate(
            ['type' => 'legal_notice'],
            [
                'title' => 'Legal Notice',
                'content' => <<<'HTML'
<h2>1. Company Information</h2>
<p><strong>Company Name:</strong> PT Dinamika Publishing International (Dinamika Publika)</p>
<p><strong>Address:</strong> Jakarta, Indonesia</p>
<p><strong>Email:</strong> admin@dinamikapublika.id</p>
<p><strong>Website:</strong> https://dinamikapublika.id</p>

<h2>2. Intellectual Property Rights</h2>
<p>All content published on this website, including text, graphics, logos, images, and other materials, is protected by applicable intellectual property laws unless otherwise stated.</p>

<h2>3. User Content</h2>
<p>Users are responsible for the content they submit or publish through this website and must ensure that such content does not violate applicable laws or the rights of others.</p>

<h2>4. Disclaimer</h2>
<p>The information provided on this website is for general informational purposes. While we strive to keep the information accurate and up to date, we do not guarantee its completeness or accuracy.</p>

<h2>5. Limitation of Liability</h2>
<p>To the extent permitted by applicable law, we shall not be liable for any loss or damage arising from the use of this website or reliance on information provided through it.</p>

<h2>6. Links to Other Websites</h2>
<p>This website may contain links to external websites. We are not responsible for the content, availability, or practices of third-party websites.</p>

<h2>7. Governing Law</h2>
<p>This Legal Notice is governed by the laws of Indonesia. Any disputes shall be subject to the jurisdiction of the courts applicable in that location.</p>

<h2>8. Contact Information</h2>
<p>For questions regarding this Legal Notice, please contact us using the contact information provided above.</p>
HTML,
                'published_at' => now(),
            ]
        );
    }
}