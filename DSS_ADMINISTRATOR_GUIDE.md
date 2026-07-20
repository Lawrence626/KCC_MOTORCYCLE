# DSS User Guide for Administrators

## Introduction

The Decision Support System (DSS) is your intelligent assistant for managing dead stock inventory. This guide explains how to use DSS features as an administrator.

---

## 🎯 What is Dead Stock?

**Dead Stock** = Products with available inventory that haven't been sold for an extended period.

### Examples:
- Engine Oil: Last sold 120 days ago, 50 units in stock
- Air Filter: Last sold 180 days ago, 30 units in stock  
- Helmet Visor: Last sold 95 days ago, 15 units in stock

### Why It Matters:
- ❌ Ties up cash in unsellable inventory
- ❌ Occupies valuable warehouse space
- ❌ Risk of expiration/obsolescence
- ❌ Reduces overall profitability

### How DSS Helps:
✅ Automatically identifies dead stock
✅ Prioritizes by urgency
✅ Provides action recommendations
✅ Tracks management actions

---

## 📊 Dead Stock Dashboard

### Accessing the Dashboard
**Navigation:** Menu → DSS → Dead Stock Management

### Dashboard Overview

#### Statistics Cards (Top Row)
- **Critical Priority**: 180+ days unsold (RED)
- **High Priority**: 120-179 days unsold (ORANGE)
- **Medium Priority**: 90-119 days unsold (YELLOW)
- **Low Priority**: 60-89 days unsold (BLUE)

#### Summary Cards
- **Total Dead Stocks**: Number of identified items
- **Total Value at Risk**: ₱ amount of dead stock
- **At-Risk Products**: Items approaching threshold

### Example Dashboard
```
┌─────────────────────────────────────────┐
│  DEAD STOCK DETECTION & MANAGEMENT      │
│                                         │
│ Critical: 5  │ High: 12 │ Medium: 18 │ Low: 7
│                                         │
│ Total: 42 items                         │
│ Value: ₱125,000                         │
│ At Risk: 8                              │
└─────────────────────────────────────────┘
```

---

## 🔍 Viewing Dead Stocks

### Step 1: Navigate to Dead Stock List
Menu → DSS → Dead Stock Management

### Step 2: Review the Table
The table shows:
| Field | Meaning |
|-------|---------|
| Product Name | Item name (clickable) |
| SKU | Stock keeping unit |
| Brand | Product brand/manufacturer |
| Current Stock | Units available |
| Stock Value | Current Stock × Unit Price |
| Days Without Sale | How long since last sale |
| Last Sold | Date of last transaction |
| Priority | Critical/High/Medium/Low |
| Recommendations | Number of actions suggested |

### Step 3: Click Product Name
Opens detailed analysis page with all recommendations

### Step 4: Review At-Risk Products
Products approaching the 90-day threshold - take action before they become dead stock

---

## 💡 Understanding Recommendations

Each dead stock product gets intelligent recommendations. Seven types are available:

### 1. **Promotional Campaign** 🎉
**What It Means:** Create marketing campaigns to boost sales

**When Suggested:** All dead stock items (90+ days unsold)

**Example:**
- Advertise in local papers
- Email campaign to past customers
- In-store signage
- Word-of-mouth campaigns

**Action Needed:** Coordinate with marketing team

---

### 2. **Price Reduction** 💰
**What It Means:** Lower the selling price to increase demand

**When Suggested:** Items 90+ days unsold

**Recommended Discount:**
- 90-119 days: 10-15% reduction
- 120-179 days: 15-20% reduction
- 180+ days: 20%+ reduction

**Example:**
- Engine Oil: ₱2,000 → ₱1,700 (15% off)
- Air Filter: ₱500 → ₱400 (20% off)

**Action Needed:** Update pricing in system

---

### 3. **Bundle Offers** 📦
**What It Means:** Combine with fast-selling items to increase value

**When Suggested:** Items 90+ days unsold + fast-moving items available

**Examples:**
- Engine Oil + Oil Filter
- Brake Pads + Brake Fluid
- Helmet + Visor Cleaner

**Why It Works:**
- Increases perceived value
- Customers buy slower items with popular items
- Increases sales velocity for both

**Action Needed:** Create bundle in system, update pricing

---

### 4. **Warehouse Relocation** 🏪
**What It Means:** Move to a location with higher demand

**When Suggested:** Items 120+ days unsold

**Options:**
- Move from back warehouse to Main Shop
- Relocate from one branch to another
- Place on prominent shelf

**Why It Works:**
- Better customer visibility
- More impulse purchases
- Different customer demographics

**Action Needed:** Coordinate with warehouse/shop staff

---

### 5. **Featured Display** ⭐
**What It Means:** Place in prominent store location

**When Suggested:** Items 90+ days unsold

**Best Locations:**
- Next to cash register
- Store entrance
- End-of-aisle display
- Sales display counter

**Why It Works:**
- Customers see items they might miss
- End-of-aisle displays increase sales by 20%+
- Register placement drives impulse buys

**Action Needed:** Arrange with shop manager

---

### 6. **Social Media Campaign** 📱
**What It Means:** Advertise online on social platforms

**When Suggested:** Items 90+ days unsold

**Platforms:**
- Facebook (most effective for retail)
- Instagram (visual products)
- TikTok (if target audience young)

**Content Ideas:**
- Product photos/videos
- Customer testimonials
- Use-case videos
- Discount announcements

**Action Needed:** Work with marketing/social media team

---

### 7. **Supplier Return** 🔄
**What It Means:** Return unsold inventory to the supplier

**When Suggested:** Items 180+ days unsold (Critical priority only)

**Requirements:**
- Must check supplier agreement
- May have restocking fees
- Frees up cash and space

**Process:**
1. Verify supplier allows returns
2. Calculate refund amount
3. Arrange logistics
4. Update inventory in system

**Action Needed:** Contact supplier for return authorization

---

## 📋 Recommendation Workflow

### Step 1: View Recommendation
Navigate to: DSS → Recommendations

### Step 2: Review Details
Click recommendation to see:
- Detailed description
- Why it's recommended
- Specific action items
- Required resources

### Step 3: Take Action
Choose one or more:
- **Do It Now**: Implement immediately
- **Schedule**: Plan for specific date
- **Delegate**: Assign to team member
- **Skip**: Not applicable (with notes)

### Step 4: Document Action
Click "Mark as Actioned" and note what was done:
- Action Taken (e.g., "Created Facebook campaign")
- Date Completed
- Results (optional)

### Step 5: Track Results
Monitor if action improved sales:
- Check dead stock status after 30 days
- Compare velocity before/after
- Adjust approach if needed

---

## ⚙️ Configuring Settings

### Accessing Settings
Menu → DSS → Settings

### Adjustable Settings

#### 1. Dead Stock Threshold
**Current:** 90 days
**Options:** 30, 60, 90, 180 days
**Consider:**
- 30 days: Very aggressive, high false positives
- 60 days: For fast-moving items (seasonal)
- **90 days: Standard retail (recommended)**
- 180 days: For slow-moving items

**When to Change:**
- Seasonal business: Adjust quarterly
- New products: Lower threshold first 90 days
- Slow-moving categories: Increase threshold

#### 2. Slow Moving Threshold
**Current:** 60 days
**Meaning:** Items unsold 60+ days are tracked as "slow moving"
**Use:** For early warnings before dead stock

#### 3. Fast Moving Threshold
**Current:** 50 units/month
**Meaning:** Items selling 50+ units/month are "fast moving"
**Use:** For bundle recommendations
**Adjust:** 
- If store is busy: Increase to 75-100
- If slow: Decrease to 25-30

#### 4. Feature Toggles
Enable/disable each recommendation type:
- ✓ Promotional Campaign
- ✓ Price Reduction
- ✓ Bundle Offers
- ✓ Relocation suggestions
- (Others available)

#### 5. Automatic Analysis
- **ON (Recommended)**: Automatically updates when POS transactions occur
- **OFF**: Manual analysis only (use "Recalculate" button)

### Saving Settings
1. Adjust values
2. Click "Save Configuration"
3. Confirmation message appears
4. Settings take effect immediately

---

## 🔄 Manual Recalculation

### When to Recalculate
- After major inventory adjustments
- After POS system outages
- Monthly check (even with auto-analysis)
- To test new settings

### How to Recalculate
1. Go to: DSS → Dead Stock Management
2. Click "Recalculate Analysis" button (top right)
3. Click "Start Recalculation" in dialog
4. Wait for completion (2-10 seconds)
5. Page auto-refreshes with new data

### What Happens During Recalculation
1. **Analyzes** all products for dead stock status
2. **Calculates** sales velocity (fast vs slow moving)
3. **Generates** all applicable recommendations
4. **Updates** database with latest data

---

## 📊 Priority Levels & Actions

### Priority Levels Explained

#### 🔴 Critical (180+ days)
- **Action:** Immediate intervention required
- **Timeline:** This week
- **Recommended Actions:**
  1. Price reduction (20%+)
  2. Featured display
  3. Supplier return
  4. Liquidation sale

#### 🟠 High (120-179 days)
- **Action:** Urgent action needed
- **Timeline:** This month
- **Recommended Actions:**
  1. Promotional campaign
  2. Price reduction (15-20%)
  3. Bundle offers
  4. Relocation

#### 🟡 Medium (90-119 days)
- **Action:** Action recommended
- **Timeline:** This quarter
- **Recommended Actions:**
  1. Price reduction (10-15%)
  2. Bundle offers
  3. Social media campaign
  4. Featured display

#### 🔵 Low (60-89 days)
- **Action:** Monitor & prepare
- **Timeline:** Ongoing
- **Recommended Actions:**
  1. Social media exposure
  2. Monitor sales trends
  3. Prepare promotional plan

---

## 📈 Best Practices

### Daily Habits
✓ Check dashboard each morning
✓ Note any new critical items
✓ Plan actions for medium/high priority

### Weekly Tasks
✓ Review recommendations list
✓ Mark completed actions
✓ Delegate new actions
✓ Monitor progress on recent actions

### Monthly Reviews
✓ Run manual recalculation
✓ Adjust settings if needed
✓ Review effectiveness of actions
✓ Plan promotions/sales

### Quarterly Reviews
✓ Analyze trends in dead stock
✓ Adjust thresholds if needed
✓ Review supplier agreement terms
✓ Plan seasonal adjustments

---

## 🎯 Action Templates

### Promotional Campaign
1. Identify target customers
2. Create message (discount, value prop)
3. Select channels (email, FB, signage)
4. Set duration (1-2 weeks)
5. Monitor sales lift
6. Document results

### Price Reduction
1. Calculate new price (10-20% off)
2. Update system
3. Print new labels
4. Notify staff
5. Train on new price
6. Monitor effect (should see 20-30% sales increase)

### Bundle Creation
1. Select complementary product
2. Set bundle price (usually: price1 + price2 - 10-15%)
3. Create bundle in system
4. Print bundle labels
5. Market bundle (signage, email)
6. Track bundle sales

---

## 🔍 Troubleshooting

### No Dead Stocks Showing
**Possible Cause:** New system, or all products sold recently

**Solution:**
- Check if products have stock
- Verify threshold setting (90 days?)
- Check if products are archived
- Wait for products to age

### Recommendations Not Appearing
**Possible Cause:** Automatic analysis disabled

**Solution:**
- Go to Settings
- Enable "Automatic Analysis"
- Run "Recalculate Analysis" button
- Wait 30 seconds

### Old Data Still Showing
**Possible Cause:** Analysis hasn't run recently

**Solution:**
- Click "Recalculate Analysis" button
- Or wait for automatic update after next POS transaction

### Settings Not Saving
**Possible Cause:** Browser cache or connection issue

**Solution:**
- Clear browser cache
- Try again
- Check internet connection
- Try different browser

---

## 📞 Getting Help

### Quick Reference
- **Dashboard**: View overview of dead stock situation
- **Dead Stock List**: See all items by priority
- **Product Detail**: View specific product analysis
- **Recommendations**: View and act on suggestions
- **Settings**: Configure system parameters

### For Questions
1. Check this user guide
2. Review DSS module documentation
3. Ask supervisor
4. Contact IT/development team

---

## 🎓 Training Checklist

**Ensure all administrators can:**

- [ ] Navigate to DSS dashboard
- [ ] Understand priority levels
- [ ] View dead stock details
- [ ] Read recommendations
- [ ] Mark actions as complete
- [ ] Update settings
- [ ] Recalculate analysis
- [ ] Export data
- [ ] Generate reports
- [ ] Monitor trends

---

## 💡 Tips & Tricks

### Keyboard Shortcuts
- **Enter**: Submit form
- **Escape**: Close dialog
- **Click Product Name**: View details
- **Ctrl+F**: Search table

### Quick Actions
- Use filters to show only Critical items
- Use sort to show oldest items first
- Use search to find specific products
- Use export for reporting/analysis

### Efficiency Tips
- Set aside 15 minutes daily for DSS review
- Batch similar actions together
- Delegate high-priority items to staff
- Use templates for common actions
- Track what works in your store

---

## 📊 Sample Scenario

### Day 1: Morning Review
1. Check dashboard: 5 Critical items
2. Review each one:
   - Engine Oil (210 days): High value, needs action
   - Air Filter (195 days): Popular item type
   - Helmet Visor (185 days): Niche product
3. Plan actions

### Day 1: Take Action
1. Engine Oil: Create 20% off promotion
2. Air Filter: Bundle with Engine Oil
3. Helmet Visor: Move to featured display
4. Mark all actions in system

### Day 2-7: Monitor
1. Daily check on sales
2. Document how each action performs
3. Adjust if needed

### Day 8: Follow-up
1. Check if items sold
2. If no progress: Try next recommendation
3. Document what worked
4. Close completed actions

### Result
- Dead stock moved faster
- Cash freed up
- Warehouse space available
- Process documented for next time

---

## 📚 Additional Resources

- **DSS_MODULE_DOCUMENTATION.md**: Technical details
- **DSS_API_REFERENCE.md**: API endpoints for developers
- **DSS_IMPLEMENTATION_GUIDE.md**: System setup guide

---

## 🎊 Summary

The DSS module helps you:
✅ Quickly identify dead stock
✅ Understand what actions to take
✅ Track action completion
✅ Optimize inventory management
✅ Improve cash flow

**Start Today:**
1. Go to DSS → Dead Stock Management
2. Review the dashboard
3. Click on a critical item
4. Read the recommendations
5. Take the first action!

---

**Good luck managing your inventory! 🚀**

Questions? Check the documentation or contact your administrator.
