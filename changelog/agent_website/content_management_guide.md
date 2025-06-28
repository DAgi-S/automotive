# Website Content Management System - User Guide

## Overview
The Nati Automotive website now features a comprehensive content management system that allows you to control all website content through an easy-to-use admin interface. No technical knowledge is required to update the website content.

## Accessing the Content Management System

### Step 1: Login to Admin Panel
1. Navigate to: `https://www.natiautomotive.com/admin/`
2. Login with your admin credentials
3. Look for "Website Content" in the left sidebar navigation

### Step 2: Access Content Management
1. Click on "Website Content" in the sidebar (globe icon)
2. You'll see the content management interface with tabbed navigation

## Content Management Features

### 🎠 Hero Carousel Management
**Purpose**: Manage the main banner slides on the homepage

**How to Use:**
- **View Slides**: All current slides are displayed with their status
- **Add New Slide**: Click "Add Slide" button
  - Enter title, subtitle, button text, and button link
  - Specify background image path (e.g., `assets/images/homescreen/auto1.jpg`)
  - Set display order and active status
- **Edit Slide**: Click the edit icon (pencil) next to any slide
- **Delete Slide**: Click the delete icon (trash) next to any slide

**Tips:**
- Keep titles short and impactful
- Ensure button links are valid (use relative paths like `services.php`)
- Use high-quality images for backgrounds

### 📊 Analytics Section
**Purpose**: Control the business metrics displayed on homepage

**Current Metrics:**
- Services Provided (completed appointments)
- Registered Clients (total customers)
- Vehicles Serviced (registered vehicles)
- Expert Technicians (total workers)
- App Users (active appointment users)
- Total Bookings (all appointments)

**How to Configure:**
- Go to "Settings" tab
- Modify "Analytics Section Title" and "Analytics Section Subtitle"
- Enable/disable the analytics section display

### 💬 Testimonials Management
**Purpose**: Manage customer reviews and feedback

**How to Use:**
- **Add Testimonial**: Click "Add Testimonial"
  - Enter customer name and title
  - Write the testimonial text
  - Select star rating (1-5 stars)
  - Set display order
- **Edit/Delete**: Use the action buttons next to each testimonial

**Best Practices:**
- Keep testimonials authentic and specific
- Include customer titles for credibility
- Rotate testimonials regularly for freshness

### ❓ FAQ Management
**Purpose**: Manage frequently asked questions

**Categories Available:**
- General
- Services
- Products
- Appointments
- Billing

**How to Use:**
- **Add FAQ**: Click "Add FAQ"
  - Enter clear, concise question
  - Provide detailed answer
  - Select appropriate category
- **Organize**: Use display order to prioritize important questions

### 📱 Social Media Management
**Purpose**: Control social media platform links and branding

**Current Platforms:**
- WhatsApp (customer support)
- Telegram (community channel)
- TikTok (automotive content)

**How to Add Platform:**
- Click "Add Platform"
- Enter platform name and description
- Specify Font Awesome icon class (e.g., `fab fa-facebook`)
- Set platform URL and brand color
- Choose display order

### ⚙️ Website Settings
**Purpose**: Global website configuration

**Available Settings:**
- **General**: Site title, tagline, contact information
- **Contact**: Phone, email, address
- **Homepage**: Section limits, analytics configuration
- **Display**: Carousel settings, autoplay options

**How to Update:**
- Go to "Settings" tab
- Modify any setting value
- Click "Save All Settings"
- Changes apply immediately to the website

## Section Visibility Control

### Enable/Disable Homepage Sections
Each section of the homepage can be individually controlled:

1. **Hero Carousel**: Main banner slides
2. **Analytics**: Business metrics display
3. **Featured Services**: Service showcase
4. **Latest Products**: Product highlights
5. **Recent Blogs**: Blog post previews
6. **Clients**: Customer showcase
7. **Testimonials**: Customer reviews
8. **FAQs**: Question and answer section
9. **Social Connect**: Social media integration

**Note**: Section visibility is controlled through the database. Contact your developer to enable/disable sections.

## Configurable Display Limits

You can control how many items are shown in each section:

- **Featured Services**: Default 3 (configurable)
- **Latest Products**: Default 3 (configurable)
- **Recent Blogs**: Default 3 (configurable)
- **Clients**: Default 4 (configurable)

**To Change Limits:**
1. Go to "Settings" tab
2. Find the relevant setting (e.g., "Featured Services Limit")
3. Change the number
4. Save settings

## Business Hours & Quick Links

### Business Hours
- Automatically displays in the sidebar
- Shows current operating schedule
- Handles closed days and special notes

### Quick Links
- Sidebar navigation shortcuts
- Links to important pages
- Customizable icons and descriptions

## Real-Time Updates

**Important**: All changes you make in the content management system appear immediately on the website. There's no need to "publish" or wait for updates.

## Content Guidelines

### Writing Tips
- **Headlines**: Keep them clear and action-oriented
- **Descriptions**: Be concise but informative
- **CTAs**: Use action words like "Learn More", "Shop Now", "Contact Us"

### Image Guidelines
- Use high-quality, automotive-related images
- Ensure images are properly sized for web
- Store images in appropriate folders (`assets/images/`)

### SEO Best Practices
- Include relevant keywords naturally in content
- Keep meta descriptions under 160 characters
- Use descriptive titles for all content

## Troubleshooting

### Common Issues
1. **Changes Not Appearing**: Clear browser cache and refresh
2. **Images Not Loading**: Check image path and file permissions
3. **Forms Not Working**: Ensure all required fields are filled

### Getting Help
- Check the error messages displayed in the admin panel
- Contact your web developer for technical issues
- Keep content backups when making major changes

## Content Strategy Recommendations

### Regular Updates
- **Weekly**: Review and update testimonials
- **Monthly**: Add new FAQ entries based on customer questions
- **Quarterly**: Update hero carousel slides for seasonal promotions
- **As Needed**: Adjust business hours for holidays or special events

### Content Calendar
- Plan seasonal content in advance
- Rotate testimonials to showcase different services
- Update social media descriptions based on current campaigns
- Adjust analytics section titles for special promotions

---

**Last Updated**: 2025-01-21  
**System Version**: Content Management System v1.0  
**Support**: Contact your web development team for technical assistance 