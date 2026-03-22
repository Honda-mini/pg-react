import { Shield, Clock, DollarSign, Award } from 'lucide-react';

const features = [
  {
    icon: Shield,
    title: 'Quality Guarantee',
    description: 'All vehicles sold at screen price come with a 3 month or 3,000 mile warranty by PGS and, if due, have a full service & new cam belt.* '
  },
  {
    icon: Clock,
    title: 'Flexible Hours',
    description: 'Open 7 days a week with viewing at evenings as well both by appointment.'
  },
  {
    icon: DollarSign,
    title: 'Best Prices',
    description: 'Competitive pricing, cards accepted, p/x possible. To include a new or long MOT and HPi check with certificate. All vehicles valeted with Autoglym products to a very high standard'
  },
  {
    icon: Award,
    title: 'Premium Selection',
    description: 'over 30 years motor trade experience and passion for cars means we have a wide range of quality used cars to choose from.'
  }
  
];

export function InfoSection() {
  return (
    <section className="py-20 bg-white dark:bg-gray-900" id="about">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Why Choose PG Services?
          </h2>
          <p className="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
            We're committed to providing exceptional service and the best car buying experience
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {features.map((feature, index) => {
            const Icon = feature.icon;
            return (
              <div
                key={index}
                className="text-center p-6 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors"
              >
                <div className="w-16 h-16 bg-blue-600 dark:bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                  <Icon className="w-8 h-8 text-white dark:text-gray-900" />
                </div>
                <h3 className="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                  {feature.title}
                </h3>
                <p className="text-gray-600 dark:text-gray-300">
                  {feature.description}
                </p>
              </div>
            );
          })}
        </div>
            <p className="mt-6 text-xs text-gray-500 dark:text-gray-400 max-w-3xl mx-auto text-left">
  *Excludes PX‑to‑clear vehicles, some sports cars, and reduced or special‑price vehicles. Delivery available.
</p>


        <div className="mt-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <h3 className="text-3xl font-bold text-gray-900 dark:text-white mb-6">
              Welcome to P.G. Services
            </h3>

            <p className="text-lg text-gray-600 dark:text-gray-300 mb-6">
              For over 30 years, we’ve been a family‑run dealership built on experience, trust, and a genuine passion for quality vehicles. Since 1994, we’ve specialised in carefully sourced, low‑mileage cars and commercials — each one prepared to a high standard and backed by our own warranty for complete peace of mind.
            </p>
            <p className="text-lg text-gray-600 dark:text-gray-300 mb-8">
              Whether you’re browsing or searching for something specific, we’re here to help you find the right vehicle, with flexible viewing times and a friendly, personal approach.
            </p>
            <a href="/about">
              <button className="px-8 py-4 bg-blue-600 dark:bg-yellow-500 text-white dark:text-gray-900 rounded-lg hover:bg-blue-700 dark:hover:bg-yellow-400 transition-colors">
                Learn More About Us
              </button>
            </a>
          </div>
          
          <div className="relative h-[400px] rounded-xl overflow-hidden shadow-2xl">
            <img
              src="/images/megane-int.jpg"
              alt="Premium car interior"
              className="w-full h-full object-cover"
            />
          </div>
        </div>
      </div>
    </section>
  );
}
