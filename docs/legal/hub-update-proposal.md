# Legal Hub & Document Update Proposal

This proposal outlines the creation of a centralized **Legal Hub** and provides specific text updates for the **Privacy Policy** and **Terms of Service** to support paid features and community contributions.

---

## 1. The "Legal Hub" Structure
Instead of individual links in the footer, we will create a centralized `/legal` page that acts as a directory for all legal documents.

### **Proposed `/legal` Page (Mockup)**
*   **Terms of Service**: The master agreement governing your use of the platform.
*   **Privacy Policy**: How we collect, use, and protect your personal data.
*   **Refund & Cancellation Policy**: Specific terms for voting credits and paid contributions.
*   **Community Guidelines**: Behavioral expectations for all members.
*   **Cookie Policy**: Details on the technical tracking we use.

---

## 2. Updated Terms of Service (Key Additions)

### **Section: Payments & Voting Credits**
> "All payments for voting credits, contribution fees, or other digital services are processed through our third-party payment provider (e.g., Stripe). By completing a transaction, you agree to their terms. **All sales are final and non-refundable** once the digital service (e.g., a vote or edit submission) has been initiated or delivered."

### **Section: Intellectual Property & Contributions**
> "By submitting an edit, suggestion, or any other content to {{ config('app.name') }}, you irrevocably grant us a perpetual, worldwide, royalty-free, and exclusive license to use, modify, publish, and incorporate that content into the final book project and any related promotional materials. You represent that you own all rights to the content you submit."

### **Section: Limitation of Liability**
> "To the maximum extent permitted by law, our total liability for any claim arising out of these terms or the Service shall not exceed the total amount paid by you to us in the twelve (12) months preceding the claim."

---

## 3. Updated Privacy Policy (Key Additions)

### **Section: Payment Information**
> "We do not store your full credit card details on our servers. Payment information is collected and processed directly by our payment provider (Stripe). We only receive and store metadata related to your transaction (e.g., transaction ID, amount, and status) for order fulfillment and tax purposes."

### **Section: Legal Basis for Processing (GDPR/CCPA)**
> "We process your data based on:
> 1. **Contractual Necessity**: To provide the services you signed up for (e.g., managing your account and votes).
> 2. **Consent**: When you opt-in to marketing or specific data uses.
> 3. **Legitimate Interest**: To maintain security, prevent fraud, and improve our platform."

### **Section: Data Retention**
> "We retain your account information as long as your account is active. Financial records are retained for a minimum of seven (7) years to comply with tax and legal obligations. Contributions (edits/votes) may be retained indefinitely as part of the project's historical record."

---

## 4. Implementation Steps

1.  **Create `resources/views/legal/index.blade.php`**: The hub page.
2.  **Update `resources/views/terms.blade.php`**: Incorporate the new clauses.
3.  **Update `resources/views/privacy.blade.php`**: Incorporate the new clauses.
4.  **Update `routes/web.php`**: Add the `/legal` route.
5.  **Update Footer**: Replace individual links with a single "Legal" link.

---

**Recommendation:** These updates should be reviewed by a legal professional before being finalized for a production environment.
