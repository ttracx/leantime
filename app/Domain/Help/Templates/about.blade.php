@extends('layouts.app')

@section('content')
<div class="pageheader">
    <div class="pageicon"><span class="fa fa-info-circle"></span></div>
    <div class="pagetitle">
        <h1>{{ __('headlines.about_safe4work') }}</h1>
    </div>
</div>

<div class="maincontent">
    <div class="maincontentinner">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-p-8 tw-mb-8">
                    <h2 class="tw-text-3xl tw-font-bold tw-mb-6 tw-text-gray-800">Empowering Through AI Innovation</h2>
                    
                    <p class="tw-text-lg tw-text-gray-700 tw-mb-6">
                        Safe4All and Safe4Work represents the culmination of decades of experience in technology leadership, 
                        product development, and AI innovation, united by a shared vision to transform care management for 
                        neurodiverse individuals.
                    </p>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6 tw-mb-8">
                        <div class="tw-text-center tw-p-6 tw-bg-primary-50 tw-rounded-lg">
                            <i class="fa fa-brain tw-text-4xl tw-text-primary-600 tw-mb-4"></i>
                            <h3 class="tw-font-bold tw-text-xl tw-mb-2">AI-First Approach</h3>
                            <p class="tw-text-gray-700">Leveraging the latest advances in artificial intelligence, machine learning, 
                            and quantum computing to create truly intelligent care management systems.</p>
                        </div>
                        <div class="tw-text-center tw-p-6 tw-bg-primary-50 tw-rounded-lg">
                            <i class="fa fa-heart tw-text-4xl tw-text-primary-600 tw-mb-4"></i>
                            <h3 class="tw-font-bold tw-text-xl tw-mb-2">Human-Centered Design</h3>
                            <p class="tw-text-gray-700">Every feature is designed with empathy, accessibility, and the unique 
                            needs of neurodiverse individuals at the forefront.</p>
                        </div>
                        <div class="tw-text-center tw-p-6 tw-bg-primary-50 tw-rounded-lg">
                            <i class="fa fa-shield-alt tw-text-4xl tw-text-primary-600 tw-mb-4"></i>
                            <h3 class="tw-font-bold tw-text-xl tw-mb-2">Trust & Security</h3>
                            <p class="tw-text-gray-700">Building secure, HIPAA-compliant solutions that protect privacy while 
                            enabling meaningful connections and support.</p>
                        </div>
                    </div>
                </div>

                <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-p-8 tw-mb-8">
                    <h2 class="tw-text-2xl tw-font-bold tw-mb-6 tw-text-gray-800">Our Mission</h2>
                    <p class="tw-text-lg tw-text-gray-700 tw-mb-6">
                        To revolutionize care management through cutting-edge AI technology, creating personalized, accessible, 
                        and empowering solutions that promote independence and improve quality of life for neurodiverse individuals 
                        and their families.
                    </p>
                </div>

                <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-p-8 tw-mb-8">
                    <h2 class="tw-text-2xl tw-font-bold tw-mb-6 tw-text-gray-800">Leadership Team</h2>
                    <p class="tw-text-gray-700 tw-mb-6">
                        Our founding team brings together decades of experience in technology leadership, product development, 
                        and AI innovation to create transformative solutions.
                    </p>

                    <div class="tw-space-y-6">
                        <div class="tw-border-l-4 tw-border-primary-500 tw-pl-6">
                            <h3 class="tw-font-bold tw-text-xl tw-text-gray-800">Jim Ross</h3>
                            <p class="tw-text-primary-600 tw-font-semibold tw-mb-2">Co-founder, Board Chairman & CEO</p>
                            <p class="tw-text-gray-700">
                                Jim Ross is a market focused team building leader who has established long term meaningful business 
                                successes and trusted relationships for 50 years. Jim's business goals are to create shareholder and 
                                market value, communicate with personal contact and earn customer satisfaction referrals with exceptional 
                                performance. Jim has performed successfully as Chairman & CEO in private software companies, President & 
                                COO of a public software company, Board Member of several private software companies and an advisor and 
                                consultant to numerous start-up companies.
                            </p>
                        </div>

                        <div class="tw-border-l-4 tw-border-primary-500 tw-pl-6">
                            <h3 class="tw-font-bold tw-text-xl tw-text-gray-800">Craig Ross</h3>
                            <p class="tw-text-primary-600 tw-font-semibold tw-mb-2">Co-founder, Board Member, & Chief Product Officer</p>
                            <p class="tw-text-gray-700">
                                Craig is a proven operations and experienced technology business entrepreneur/owner with over 25 years 
                                of business development, general management and successful software product launch experiences. Craig is 
                                a leader, motivator, excellent communicator and close to customers with outstanding relationship soft skills. 
                                For the past 12 years, Craig was President & COO of a software product company which, among many other 
                                successes, successfully deployed software globally into every English speaking country.
                            </p>
                        </div>

                        <div class="tw-border-l-4 tw-border-primary-500 tw-pl-6">
                            <h3 class="tw-font-bold tw-text-xl tw-text-gray-800">Dick Layton</h3>
                            <p class="tw-text-primary-600 tw-font-semibold tw-mb-2">Co-founder, Board Member, & Chief Technology Officer</p>
                            <p class="tw-text-gray-500 tw-italic tw-mb-2">In loving memory</p>
                            <p class="tw-text-gray-700">
                                Dick brings over 30 years of technology leadership experience in building scalable enterprise solutions 
                                and leading high-performance engineering teams. As Chief Technology Officer, Dick has successfully 
                                architected and deployed mission-critical systems for Fortune 500 companies, pioneered cloud-native 
                                architectures, and championed innovative approaches to software development that have transformed how 
                                businesses leverage technology.
                            </p>
                        </div>

                        <div class="tw-border-l-4 tw-border-primary-500 tw-pl-6">
                            <h3 class="tw-font-bold tw-text-xl tw-text-gray-800">Tommy Xaypanya</h3>
                            <p class="tw-text-primary-600 tw-font-semibold tw-mb-2">CTO</p>
                            <p class="tw-text-gray-700">
                                An accomplished AI leader with over 18 years of experience driving innovation in artificial intelligence, 
                                machine learning, and quantum computing integration. Tommy brings deep expertise as Chief AI Officer, 
                                having architected enterprise-scale AI systems, led cross-functional teams in developing industry-specific 
                                solutions, and pioneered quantum-AI research for transformative business applications.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-p-8 tw-mb-8">
                    <h2 class="tw-text-2xl tw-font-bold tw-mb-6 tw-text-gray-800">Technology Partners</h2>
                    <p class="tw-text-gray-700 tw-mb-6">
                        Safe4All and Safe4Work is powered by cutting-edge AI technology from our strategic partners, combining 
                        quantum computing and neural network innovations.
                    </p>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                        <div class="tw-border tw-border-gray-200 tw-rounded-lg tw-p-6">
                            <h3 class="tw-font-bold tw-text-xl tw-text-gray-800 tw-mb-2">NeuralQuantum.ai</h3>
                            <p class="tw-text-primary-600 tw-font-semibold tw-mb-2">Quantum-AI Integration</p>
                            <p class="tw-text-gray-700">
                                Leading the frontier of quantum-enhanced artificial intelligence, NeuralQuantum.ai provides the 
                                advanced computational foundation that powers Safe4All's sophisticated multi-agent AI system.
                            </p>
                        </div>

                        <div class="tw-border tw-border-gray-200 tw-rounded-lg tw-p-6">
                            <h3 class="tw-font-bold tw-text-xl tw-text-gray-800 tw-mb-2">VibeCaaS and Tunaas.ai</h3>
                            <p class="tw-text-primary-600 tw-font-semibold tw-mb-2">Innovation Platform</p>
                            <p class="tw-text-gray-700">
                                A pioneering innovation platform specializing in accessible AI solutions, Tunaas.ai contributes 
                                essential accessibility frameworks and neurodiverse-focused design patterns that make Safe4All 
                                and Safe4Work truly inclusive.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-p-8 tw-mb-8">
                    <h2 class="tw-text-2xl tw-font-bold tw-mb-6 tw-text-gray-800">Our Values</h2>
                    <p class="tw-text-gray-700 tw-mb-6">
                        These core values guide every decision we make and every feature we build.
                    </p>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                        <div class="tw-flex tw-items-start">
                            <i class="fa fa-users tw-text-2xl tw-text-primary-600 tw-mr-4 tw-mt-1"></i>
                            <div>
                                <h3 class="tw-font-bold tw-text-lg tw-mb-2">Inclusivity</h3>
                                <p class="tw-text-gray-700">Designing for everyone, with special attention to neurodiverse needs 
                                and accessibility.</p>
                            </div>
                        </div>
                        <div class="tw-flex tw-items-start">
                            <i class="fa fa-star tw-text-2xl tw-text-primary-600 tw-mr-4 tw-mt-1"></i>
                            <div>
                                <h3 class="tw-font-bold tw-text-lg tw-mb-2">Excellence</h3>
                                <p class="tw-text-gray-700">Striving for the highest quality in every aspect of our technology 
                                and service delivery.</p>
                            </div>
                        </div>
                        <div class="tw-flex tw-items-start">
                            <i class="fa fa-lightbulb tw-text-2xl tw-text-primary-600 tw-mr-4 tw-mt-1"></i>
                            <div>
                                <h3 class="tw-font-bold tw-text-lg tw-mb-2">Innovation</h3>
                                <p class="tw-text-gray-700">Continuously pushing the boundaries of what's possible with AI 
                                and quantum computing.</p>
                            </div>
                        </div>
                        <div class="tw-flex tw-items-start">
                            <i class="fa fa-hand-holding-heart tw-text-2xl tw-text-primary-600 tw-mr-4 tw-mt-1"></i>
                            <div>
                                <h3 class="tw-font-bold tw-text-lg tw-mb-2">Empathy</h3>
                                <p class="tw-text-gray-700">Understanding and responding to the real challenges faced by our 
                                users and their families.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tw-bg-primary-50 tw-rounded-lg tw-p-8 tw-text-center">
                    <h2 class="tw-text-2xl tw-font-bold tw-mb-4 tw-text-gray-800">Join Our Mission</h2>
                    <p class="tw-text-lg tw-text-gray-700 tw-mb-6">
                        Experience the future of AI-powered care management. Start your journey with Safe4All & Safe4Work today.
                    </p>
                    
                    <div class="tw-border-t tw-border-primary-200 tw-pt-6 tw-mt-8">
                        <h3 class="tw-font-bold tw-text-xl tw-mb-2">Contact Us</h3>
                        <p class="tw-font-semibold tw-text-primary-600 tw-mb-2">Neuro Equality LLC</p>
                        <p class="tw-text-gray-700 tw-mb-4">
                            Empowering Neurodivergent Lives Through AI, Quantum Computing, and Inclusive Design. 
                            Supporting individuals with IDD, ASD, and diverse cognitive needs worldwide.
                        </p>
                        <p class="tw-text-gray-600 tw-italic">
                            In partnership with the James Edward Ross Memorial Foundation, advancing digital equity 
                            and cognitive inclusion for IDD and ASD communities globally.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection