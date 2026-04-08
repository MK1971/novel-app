# Legal Document Comparison & Gap Analysis

This document compares the current **Privacy Policy** and **Terms of Service** of "What's My Book Name" against industry standards for paid, community-driven platforms.

---

## 1. Terms of Service (ToS) Comparison

| Feature | Current State | Industry Standard | Gap / Recommendation |
| :--- | :--- | :--- | :--- |
| **Payment Terms** | Brief mention of "payment rules apply where stated." | Detailed clauses on billing, currency, taxes, and payment processor (e.g., Stripe). | **High Gap**: Need explicit sections on how voting credits or contribution payments are handled. |
| **Refund Policy** | Not explicitly mentioned. | Clear "No Refund" or "Conditional Refund" policy for digital goods/services. | **Critical Gap**: Must state that digital contributions or votes are non-refundable once processed. |
| **IP Ownership** | Grant of rights to "operate, display, moderate." | Explicit "Contributor License Agreement" (CLA) or "Assignment of Rights." | **Medium Gap**: Clarify if the user retains any rights or if the project owns the final book. |
| **User Conduct** | General "do not misuse" clause. | Specific list of prohibited actions (scraping, botting, commercial use of content). | **Low Gap**: Add specific prohibitions against using AI to mass-generate edits. |
| **Termination** | Mention of suspension for violations. | Detailed process for account closure and data retention post-termination. | **Medium Gap**: Define what happens to a user's votes/edits if their account is deleted. |

---

## 2. Privacy Policy Comparison

| Feature | Current State | Industry Standard | Gap / Recommendation |
| :--- | :--- | :--- | :--- |
| **Data Collection** | Lists account, content, and technical data. | Granular list including payment metadata, device IDs, and location (if applicable). | **Medium Gap**: Explicitly mention payment processors (Stripe/PayPal) as third-party recipients. |
| **Legal Basis** | Not explicitly stated. | Clear "Legal Basis for Processing" (Consent, Contract, Legitimate Interest) for GDPR. | **High Gap**: Required for users in the EU/UK. |
| **Data Retention** | Not mentioned. | Specific retention periods (e.g., "7 years for financial records"). | **Medium Gap**: Define how long edit history is kept. |
| **User Rights** | Mentions access, correction, deletion. | Detailed instructions for exercising rights (DSAR process) and right to portability. | **Medium Gap**: Provide a specific email or form for legal requests. |
| **Cookies** | Brief mention. | Detailed Cookie Policy or link to a preference center. | **Low Gap**: List specific cookies used (session, CSRF, analytics). |

---

## 3. Structural Recommendations

### The "Legal Hub" Concept
Currently, links are scattered in the footer. A centralized **Legal Hub** (e.g., `/legal`) would improve trust and accessibility.

**Proposed Hub Structure:**
1.  **Terms of Service**: The master agreement.
2.  **Privacy Policy**: Data handling details.
3.  **Refund & Cancellation Policy**: Specifics for paid features.
4.  **Community Guidelines**: Behavioral expectations (less formal).
5.  **Cookie Policy**: Technical tracking details.

---

## 4. Key Text Updates Needed

1.  **Monetization Clause**: "Payments for voting credits or contributions are processed via [Processor]. All transactions are final."
2.  **IP Assignment**: "By submitting an edit, you irrevocably assign all copyright and intellectual property rights in that edit to [Project Name] for use in the final publication."
3.  **Liability Cap**: Explicitly limit liability to the amount paid by the user in the last 12 months.
