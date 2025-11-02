# Secure-QR-Code-Generator-Integrate-With-Api
A Final year project which has priority to address societal issues related to QR code fraud cases. Dreaming to develop secure and trusted platform to generate QR code without on relying on any third party.

The Secure QR Code Generator is built to provide more than just a typical QR creation experience. It ensures that every piece of data is protected with a strong security foundation from the moment it is uploaded until it is accessed. This project focuses on creating a secure environment where every QR code is not only functional but also trustworthy.

The system includes a built-in malware scanning feature powered by the OPSWAT Metadefender API. Each uploaded file, whether text, image, or document, is automatically scanned for hidden threats before the QR code is generated. To further enhance data protection, the system uses ChaCha20 Poly1305 encryption combined with unique keys, salts, and nonces, giving each QR code its own distinct digital fingerprint.

Before anyone can access the content of a QR code, a tri-layer verification process ensures that only authorized users can proceed. This verification process includes password or one-time passcode authentication, maintaining strict access control. Users can also share their QR codes securely either through email or with verified friends, ensuring privacy and preventing unauthorized access.

The Secure QR Code Generator was designed with one clear goal: to protect user information without compromise. Every component of the system works together to guarantee that sensitive data remains private and trustworthy. When it comes to data protection, this project represents a commitment to building secure solutions that prioritize safety and reliability in every scan.

Features of Secure QR Code Generator

Malware Scanning Integration
The system integrates the OPSWAT Metadefender API to automatically scan all uploaded files, including text, images, and documents. This ensures that any potential malware or malicious content is detected and removed before a QR code is generated.

Advanced Encryption Mechanism
The project employs the ChaCha20 Poly1305 encryption algorithm in combination with the Argon2id key derivation function. Each QR code is generated with a unique key, salt, and nonce, providing strong data confidentiality and ensuring that every QR code possesses a distinct digital identity.

Tri-Layer Verification Process
A multi-level verification process is implemented to ensure secure access to QR content. Users are required to authenticate themselves through password or one-time passcode (OTP) verification before gaining access, guaranteeing that only authorized individuals can view the information.

Secure Content Sharing
The system allows users to share QR codes safely with verified recipients through email or within their account environment. Access control mechanisms prevent unauthorized access, ensuring that sensitive content remains private at all times.

Comprehensive Software Testing
The system will undergone multiple testing stages, including black box testing, database testing, performance testing, and SQL injection testing. Both manual and automated approaches were conducted using tools such as Cypress, JMeter, and SonarQube to ensure software reliability and security.

User-Centric Design and Functionality
The platform is designed with a user-friendly interface that prioritizes both usability and security. It ensures smooth navigation while maintaining strict data protection standards throughout the user interaction process.

Compliance with Secure Development Practices
The entire development follows the Software Development Life Cycle (SDLC) methodology and adheres to real-world industry documentation and testing standards, ensuring high software quality and secure system architecture.
