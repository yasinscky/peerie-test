<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hashtag;

class HashtagSeeder extends Seeder
{
    public function run(): void
    {
        $beautyBroad = [
            '#HairCareTips', '#HairCareProducts', '#Hairdressing', '#ShortHairstyles', '#HairstylesForGirls', '#HairstylesForMen', '#CurlyHairstyles',
            '#HairstylistLife', '#UpdoHairstyles', '#HairExtensionsSpecialist', '#WeddingMakeupArtist', '#WeddingHairstyles', '#BridesmaidHair',
            '#CurlyHairCare', '#MensHairstyles', '#MensHaircuts', '#NailTechnician', '#NailArtDesign', '#GelNailsDesign', '#BIABNails',
            '#NailExtensions', '#NailDesigner', '#EyelashArtist', '#LashTrainer', '#BrowLamination', '#NaturalSkincareProducts', '#SkincareEssentials',
            '#CosmeticSurgeon', '#PlasticSurgeon', '#FacialRejuvenation', '#DermalFiller', '#LipFillerBeforeAndAfter', '#BotoxInjections',
            '#EstheticianStudent', '#WeddingHairstylist', '#BridalMakeupAndHair'
        ];

        $beautyIndustryGeneral = [
            '#PamperTime', '#ConfidenceBooster', '#EventReady', '#BeautyTherapistLife', '#BehindTheChair', '#CleanGirlAesthetic',
        ];
        
        $beautyIndustryHairSalons = [
            '#ColourSpecialist', '#BehindTheHair', '#TrendyHaircuts', '#EditorialHair', '#EditorialHairStylist', '#EasyHairstyles',
            '#EasyHairstylesForGirls', '#CurlyHairMethod', '#CurlyHairInspo', '#HairTok', '#FrenchBob', '#CopperHairColour', '#BarbieBlonde',
        ];
        
        $beautyIndustryWedding = [
            '#WeddingHairSpecialist', '#WeddingHairdresser', '#BridalUpdoIdeas', '#WeddingMakeupIdeas', '#WeddingMakeupLook', '#BridalHairIdeas',
            '#WeddingHairInspiration', '#WeddingHeadpiece', '#BridesmaidHairstyle', '#BridalHeadpiece', '#BridalHairAccessories', '#WeddingHairInspo',
        ];
        
        $beautyIndustryNails = [
            '#AcrylicNailDesign', '#NailDesignSwag', '#HaileyBieberNails',
        ];
        
        $beautyIndustryLashesBrows = [
            '#EyelashExtensionsBy', '#TechniqueMatters', '#LashBeautyBar', '#EyelashExtensionTraining', '#ClassicSetLashes', '#LashTechLife',
            '#LashLifestyle', '#BrowShapingSpecialist', '#BrowTints', '#BrowTintAndWax', '#HDBrowsStylist',
        ];
        
        $beautyIndustrySkincareFacials = [
            '#SkincareDailyRoutine', '#SkinExperts', '#EstheticianTips', '#EstheticianBlogger', '#MedicalEsthetician', '#SkincareReviewer',
            '#SkincareTreatments', '#SkincareSpecialist', '#SkincareInfluencer', '#SkinGlowUp', '#GlowFacial', '#Slugging', '#SkinCycling',
            '#LicensedAesthetician', '#MedicalAesthetician',
        ];
        
        $beautyIndustrySpaWellness = [
            '#WellnessSpa', '#WellnessSpace', '#SpaAndWellness', '#SpaDays', '#SpaExperience', '#SpaTreatments', '#SpaMassage', '#SpaFacial',
        ];
        
        $beautyIndustryAdvancedAesthetics = [
            '#AntiWrinkleInjections', '#AntiWrinkleTreatment', '#RhinoplastySchool', '#NoseFiller', '#DermalFillerTraining', 
            '#MedSpaLife', '#FacialPlasticSurgeon', '#SkinBoosterFacial', '#SkinBoosterTreatment', '#WrinkleFreeSkin'
        ];

        $beautyNiche = ['#BohoBridalMakeup'];
        
        $beautyNicheDE = [
            '#NaturkosmetikMünchen', '#ReifereHaut', '#Kopfhautgesundheit', '#BioNägel', '#NachhaltigerFriseur'
        ];
        
        $beautyBranded = [
            ['tag' => '#GlowWithGrace', 'description' => 'signature facial programme'],
            ['tag' => '#BeautyByAria', 'description' => 'brand-specific tag'],
            ['tag' => '#SkinGoalsChallenge', 'description' => 'campaign hashtag'],
            ['tag' => '#BrowsByLena', 'description' => 'service-specific branding'],
            ['tag' => '#ClientGlowUp', 'description' => 'testimonial series tag'],
            ['tag' => '#NailedItFriday', 'description' => 'weekly content series'],
            ['tag' => '#SummerSkinBootcamp', 'description' => 'limited-time promo tag'],
        ];
        
        $beautyBrandedDE = [
            ['tag' => '#DieGlowMethode', 'description' => 'Signatur-Behandlung'],
            ['tag' => '#TransformationDonnerstag', 'description' => 'wiederkehrende Serie'],
            ['tag' => '#VorherNachherMitMaria', 'description' => 'Stylistin-Serie'],
            ['tag' => '#DeinWellnessMoment', 'description' => 'Markenerlebnis'],
            ['tag' => '#BeautyByBerlin', 'description' => 'Standort-Marke'],
            ['tag' => '#GlowUpChallenge', 'description' => 'Kampagnen-Tag'],
            ['tag' => '#KundenliebeMontag', 'description' => 'Testimonial-Serie'],
            ['tag' => '#SalonGeheimnisse', 'description' => 'Bildungs-Content-Serie'],
            ['tag' => '#MeineBeautyReise', 'description' => 'Kundengeschichten-Kampagne'],
        ];
        
        $beautyBroadDE = [
            '#HairCareTips', '#HairCareProducts', '#Hairdressing', '#ShortHairstyles', '#HairstylesForGirls', '#HairstylesForMen', '#CurlyHairstyles',
            '#HairstylistLife', '#UpdoHairstyles', '#HairExtensionsSpecialist', '#WeddingMakeupArtist', '#WeddingHairstyles', '#BridesmaidHair',
            '#CurlyHairCare', '#MensHairstyles', '#MensHaircuts', '#NailTechnician', '#NailArtDesign', '#GelNailsDesign', '#BIABNails',
            '#NailExtensions', '#NailDesigner', '#EyelashArtist', '#LashTrainer', '#BrowLamination', '#NaturalSkincareProducts', '#SkincareEssentials',
            '#CosmeticSurgeon', '#PlasticSurgeon', '#FacialRejuvenation', '#DermalFiller', '#LipFillerBeforeAndAfter', '#BotoxInjections',
            '#Haarschnitt', '#Haarfarbe', '#Wimpernlifting', '#Gesichtsbehandlung', '#VorherNachher', '#Kosmetikstudio', '#FriseurSalon',
            '#Haarverlängerung', '#Haarpflege', '#Brautfrisur', '#MineralMakeup'
        ];
        
        $beautyIndustryGeneralDE = [
            '#PamperTime', '#ConfidenceBooster', '#EventReady', '#BeautyTherapistLife', '#BehindTheChair', '#CleanGirlAesthetic',
            '#Selbstpflege', '#FühlDichSchön', '#Kosmetikerin', '#BeautyBehandlung'
        ];
        
        $beautyIndustryHairSalonsDE = [
            '#ColourSpecialist', '#BehindTheHair', '#TrendyHaircuts', '#EditorialHair', '#EditorialHairStylist', '#EasyHairstyles',
            '#EasyHairstylesForGirls', '#CurlyHairMethod', '#CurlyHairInspo', '#HairTok', '#FrenchBob', '#CopperHairColor', '#BarbieBlonde',
            '#Haarverlängerung', '#Lockenpflege', '#BlondSpezialist'
        ];
        
        $beautyIndustryWeddingDE = [
            '#WeddingHairStylist', '#WeddingHairSpecialist', '#WeddingHairdresser', '#BridalMakeupAndHair', '#BridalUpdoIdeas', '#WeddingMakeupIdeas',
            '#WeddingMakeupLook', '#BridalHairIdeas', '#WeddingHairInspiration', '#WeddingHeadpiece', '#BridesmaidHairStyle', '#BridalHeadpiece',
            '#BridalHairAccessories', '#WeddingHairInspo', '#Brautfrisuren', '#BrautMakeup'
        ];
        
        $beautyIndustryNailsDE = [
            '#AcrylicNailDesign', '#NailDesignSwag', '#HaileyBieberNails',
        ];
        
        $beautyIndustryLashesBrowsDE = [
            '#EyelashExtensionsBy', '#TechniqueMatters', '#LashBeautyBar', '#EyelashExtensionTraining', '#ClassicSetLashes', '#LashTechLife',
            '#LashLifestyle', '#BrowShapingSpecialist', '#BrowTints', '#BrowTintAndWax', '#HDBrowStylist',
        ];
        
        $beautyIndustrySkincareFacialsDE = [
            '#EstheticianStudent', '#SkincareDailyRoutine', '#SkinExperts', '#EstheticianTips', '#EstheticianBlogger', '#MedicalEsthetician',
            '#SkincareReviewer', '#SkincareTreatments', '#SkincareSpecialist', '#SkincareInfluencer', '#SkinGlowUp', '#GlowFacial', '#Slugging',
            '#SkinCycling', '#VeganeKosmetik', '#SensibleHaut'
        ];
        
        $beautyIndustrySpaWellnessDE = [
            '#WellnessSpa', '#WellnessSpace', '#SpaAndWellness', '#SpaDays', '#SpaExperience', '#SpaTreatments', '#SpaMassage', '#SpaFacial',
        ];
        
        $beautyIndustryAdvancedAestheticsDE = [
            '#AntiWrinkleInjections', '#AntiWrinkleTreatment', '#RhinoplastySchool', '#NoseFiller', '#DermalFillerTraining',
            '#MedSpaLife', '#FacialPlasticSurgeon', '#SkinBoosterFacial', '#SkinBoosterTreatment', '#WrinkleFreeSkin',
            '#AestheticClinics', '#SkinClinics', '#MedicalAestheticsClinic', '#MedicalAestheticsTraining', '#ÄsthetischeMedizin'
        ];

        $physioBroad = [
            '#Physio', '#Physiotherapy', '#Physiotherapist', '#SportsPhysio', '#Rehab', '#InjuryRecovery', '#ManualTherapy',
            '#ExerciseRehab', '#PainRelief', '#Mobility', '#PostureCorrection', '#Musculoskeletal', '#NeuroPhysio', '#PelvicHealth',
            '#PhysioClinic', '#EvidenceBased', '#PhysioTips', '#FunctionalMovement', '#MovementIsMedicine', '#MovePainFree',
            '#StrongerEveryDay', '#ImprovedMobility', '#PosturePerfect', '#BackPainFree', '#ActiveLifestyle', '#RehabSuccess',
            '#ConfidenceInMotion', '#PhysioLife'
        ];

        $physioIndustrySportsMusculoskeletal = [
            '#SportsPhysiotherapy', '#SportsPhysiotherapist', '#InjuryPrevention', '#ACLRehab', '#RotatorCuff', '#ReturnToSport',
            '#StrengthAndConditioning', '#MobilityDrills', '#RunningInjury', '#SportsMassage', '#PerformancePhysio', '#KneeRehab',
            '#ShoulderRehab', '#AnkleStability', '#FoamRolling', '#DynamicWarmUp'
        ];
        
        $physioIndustryNeuro = [
            '#NeuroPhysiotherapy', '#StrokeRehab', '#NeuroRehab', '#ParkinsonsExercise', '#GaitTraining', '#BalanceTherapy',
            '#BrainInjuryRecovery', '#FunctionalNeuro', '#SpasticityManagement', '#Neuroplasticity'
        ];
        
        $physioIndustryPelvicWomensHealth = [
            '#PelvicFloorPhysiotherapy', '#PrenatalPhysio', '#PostnatalRecovery', '#DiastasisRecti', '#CoreRehab', '#BladderHealth',
            '#WomensHealthPhysio', '#MummyMOT'
        ];
        
        $physioIndustryPaediatric = [
            '#PaediatricPhysio', '#KidsPhysio', '#MotorDevelopment', '#Torticollis', '#CerebralPalsyCare', '#EarlyIntervention',
            '#PlayBasedTherapy', '#GaitTrainingKids', '#Hypermobility'
        ];
        
        $physioIndustryGeriatric = [
            '#SeniorFitness', '#FallsPrevention', '#HealthyAgeing', '#BalanceTraining', '#OsteoporosisExercise', '#JointHealth',
            '#ActiveAgeing', '#LaterLifePhysio'
        ];
        
        $physioIndustryChiropractic = [
            '#Chiropractor', '#ChiropracticAdjustment', '#ChiropracticWorks', '#ChiropracticHealth', '#NeckPainRelief', '#BackPainRelief',
            '#PainManagement', '#ChiropracticCare', '#SpineHealth', '#IntegrativeMedicine'
        ];
        
        $physioIndustryMassageTherapy = [
            '#MassageTherapyLife', '#TherapyMassage', '#MassageIsTherapy', '#SwedishMassageTherapy', '#HotStoneMassageTherapy',
            '#AromatherapyMassage', '#DeepTissueMassage', '#MassageForRelaxation', '#SportsMassageTherapy', '#TriggerPointMassageTherapy',
            '#ReflexologyMassage', '#ShiatsuMassageTherapy', '#GettingAMassage', '#MassageEveryday', '#MassageTherapyStudio',
            '#AtHomeMassageTherapy', '#MobileMassageTherapy', '#SpaDayMassageTherapy'
        ];
        
        $physioIndustryAcupuncture = [
            '#AcupunctureClinic', '#EasternMedicine', '#AcupunctureLife', '#Acupuncturist', '#TriggerPointTherapy', '#CosmeticAcupuncture',
            '#FertilityAcupuncture', '#AcupunctureWorks', '#AcupunctureHeals', '#HolisticHealthCare'
        ];
        
        $physioIndustryAnatomy = [
            '#AnatomyDrawing', '#AnatomyArt', '#AnatomyAndPhysiology', '#AnatomyStudy', '#HumanAnatomy', '#AnatomyPractice',
            '#AnatomyTrains', '#YogaAnatomy', '#NeuroAnatomy', '#PhysiotherapyStudent', '#MedicalIllustrator', '#ClinicalAnatomy',
            '#Physiology', '#MedStudentLife', '#SciArt'
        ];
        
        // Combined industry tags for English
        $physioIndustry = array_merge(
            $physioIndustrySportsMusculoskeletal,
            $physioIndustryNeuro,
            $physioIndustryPelvicWomensHealth,
            $physioIndustryPaediatric,
            $physioIndustryGeriatric,
            $physioIndustryChiropractic,
            $physioIndustryMassageTherapy,
            $physioIndustryAcupuncture,
            $physioIndustryAnatomy
        );

        $physioNiche = [
            '#BackPainClinicDublin', '#ChronicPainSupportIreland', '#PelvicFloorSpecialist', '#ManualLymphaticDrainage'
        ];
        
        $physioNicheDE = [
            '#PrivatPhysio', '#Rückenschmerz', '#ChronischeSchmerzStörung', '#ManuelleLymphdrainage',
            '#SportMasseur', '#PhysioKöln', '#MassageBerlin'
        ];
        
        $physioBranded = [
            ['tag' => '#ThrivePhysioCare', 'description' => 'clinic or method name'],
            ['tag' => '#JointHealthWeek', 'description' => 'seasonal or awareness-driven campaign'],
            ['tag' => '#StrongAfterSurgery', 'description' => 'rehab-specific programme tag'],
            ['tag' => '#AskDrFiona', 'description' => 'Q&A or patient education campaign'],
            ['tag' => '#MoveFreelyChallenge', 'description' => 'movement/physio awareness series'],
        ];
        
        $physioBrandedDE = [
            ['tag' => '#ThrivePhysioCare', 'description' => 'Praxis- oder Methodename'],
            ['tag' => '#GelenkGesundheitsWoche', 'description' => 'Awareness-Kampagne'],
            ['tag' => '#StarkNachOP', 'description' => 'Reha-Programm'],
            ['tag' => '#FragDrFiona', 'description' => 'Q&A-Kampagne'],
            ['tag' => '#BewegungsFreiChallenge', 'description' => 'Awareness-Serie'],
        ];
        
        $physioBroadDE = [
            '#Physio', '#Physiotherapy', '#Physiotherapist', '#SportsPhysio', '#Rehab', '#InjuryRecovery', '#ManualTherapy',
            '#ExerciseRehab', '#PainRelief', '#Mobility', '#PostureCorrection', '#Musculoskeletal', '#NeuroPhysio', '#PelvicHealth',
            '#PhysioClinic', '#EvidenceBased', '#PhysioTips', '#PhysioLife', '#FunctionalMovement', '#MovementIsMedicine', '#MovePainFree',
            '#StrongerEveryDay', '#ImprovedMobility', '#PosturePerfect', '#BackPainFree', '#ActiveLifestyle', '#RehabSuccess',
            '#ConfidenceInMotion', '#SportsPhysiotherapy', '#WorldPhysiotherapyDay', '#Akupunktur'
        ];
        
        $physioIndustrySportsMusculoskeletalDE = [
            '#Physiotherapeut', '#Physiotherapeutin', '#ChronischeSchmerzen', '#SportPhysio', '#SportPhysiotherapie',
            '#SportsPhysio', '#InjuryPrevention', '#ACLRehab', '#RotatorCuff', '#ReturnToSport', '#StrengthAndConditioning',
            '#MobilityDrills', '#RunningInjury', '#SportsMassage', '#PerformancePhysio', '#KneeRehab', '#ShoulderRehab',
            '#AnkleStability', '#FoamRolling', '#DynamicWarmUp'
        ];
        
        $physioIndustryNeuroDE = [
            '#NeuroPhysiotherapy', '#StrokeRehab', '#NeuroRehab', '#ParkinsonsExercise', '#GaitTraining', '#BalanceTherapy',
            '#BrainInjuryRecovery', '#FunctionalNeuro', '#SpasticityManagement', '#Neuroplasticity'
        ];
        
        $physioIndustryPelvicWomensHealthDE = [
            '#PelvicFloorPhysiotherapy', '#PrenatalPhysio', '#PostnatalRecovery', '#DiastasisRecti', '#CoreRehab', '#BladderHealth',
            '#WomensHealthPhysio', '#MummyMOT'
        ];
        
        $physioIndustryPaediatricDE = [
            '#PaediatricPhysio', '#KidsPhysio', '#MotorDevelopment', '#Torticollis', '#CerebralPalsyCare', '#EarlyIntervention',
            '#PlayBasedTherapy', '#GaitTrainingKids', '#Hypermobility'
        ];
        
        $physioIndustryGeriatricDE = [
            '#SeniorFitness', '#FallsPrevention', '#HealthyAgeing', '#BalanceTraining', '#OsteoporosisExercise', '#JointHealth',
            '#ActiveAgeing', '#LaterLifePhysio'
        ];
        
        $physioIndustryChiropracticDE = [
            '#Chiropractor', '#ChiropracticAdjustment', '#ChiropracticWorks', '#ChiropracticHealth', '#NeckPainRelief', '#BackPainRelief',
            '#PainManagement', '#ChiropracticCare', '#SpineHealth', '#IntegrativeMedicine'
        ];
        
        $physioIndustryMassageTherapyDE = [
            '#MassageTherapyLife', '#TherapyMassage', '#MassageIsTherapy', '#SwedishMassageTherapy', '#HotStoneMassageTherapy',
            '#AromatherapyMassage', '#DeepTissueMassage', '#MassageForRelaxation', '#SportsMassageTherapy', '#TriggerPointMassageTherapy',
            '#ReflexologyMassage', '#ShiatsuMassageTherapy', '#GettingAMassage', '#MassageEveryday', '#MassageTherapyStudio',
            '#AtHomeMassageTherapy', '#MobileMassageTherapy', '#SpaDayMassageTherapy'
        ];
        
        $physioIndustryAcupunctureDE = [
            '#AcupunctureClinic', '#EasternMedicine', '#AcupunctureLife', '#Acupuncturist', '#TriggerPointTherapy', '#CosmeticAcupuncture',
            '#FertilityAcupuncture', '#AcupunctureWorks', '#AcupunctureHeals', '#HolisticHealthCare'
        ];
        
        $physioIndustryAnatomyDE = [
            '#AnatomyDrawing', '#AnatomyArt', '#AnatomyAndPhysiology', '#AnatomyStudy', '#HumanAnatomy', '#AnatomyPractice',
            '#AnatomyTrains', '#YogaAnatomy', '#NeuroAnatomy', '#PhysiotherapyStudent', '#MedicalIllustrator', '#ClinicalAnatomy',
            '#Physiology', '#MedStudentLife', '#SciArt'
        ];

        $coachingBroad = [
            '#LifeCoach', '#LifeCoaching', '#BusinessCoach', '#BusinessCoaching', '#MindsetCoach', '#MindsetCoaching',
            '#LeadershipCoach', '#LeadershipCoaching', '#ExecutiveCoach', '#ExecutiveCoaching', '#PersonalDevelopmentCoach',
            '#CoachingTips', '#TransformationCoach', '#ImposterSyndrome', '#BusinessMentor', '#BusinessConsultant',
            '#StartYourOwnBusiness', '#EntrepreneurCoach', '#StartupTips', '#StartupSuccess', '#SmallBusinessCoach',
            '#SmallBusinessMarketing', '#SmallBusinessAdvice', '#SmallBusinessHelp', '#ProfessionalGrowth', '#ProductivityTips', '#ProductivityHacks'
        ];
        
        $coachingBroadDE = [
            '#LifeCoach', '#LifeCoaching', '#BusinessCoach', '#BusinessCoaching', '#MindsetCoach', '#MindsetCoaching',
            '#LeadershipCoach', '#LeadershipCoaching', '#ExecutiveCoach', '#ExecutiveCoaching', '#PersonalDevelopmentCoach',
            '#CoachingTips', '#TransformationCoach', '#ImposterSyndrome', '#BusinessMentor', '#BusinessConsultant',
            '#StartYourOwnBusiness', '#EntrepreneurCoach', '#StartupTips', '#StartupSuccess', '#SmallBusinessCoach',
            '#SmallBusinessMarketing', '#SmallBusinessAdvice', '#SmallBusinessHelp', '#ProfessionalGrowth', '#ProductivityTips', '#ProductivityHacks'
        ];

        $coachingIndustryGeneral = [
            '#GrowthMindsetCoach', '#SelfImprovementJourney', '#CoachLifestyle', '#GoalSettingTips', '#GoalSettingForSuccess',
            '#TransformationCoaching', '#AccountabilityPartner', '#ScaleYourBusiness', '#BreakthroughCoach', '#ConfidenceBuilding',
            '#HighPerformanceHabits', '#MarketingCoaching', '#SuccessCoaching', '#LifestyleCoaching', '#CoachingForLife',
            '#LifeCoachTips', '#CoachingTools', '#HolisticLifeCoach', '#HolisticLifeCoaching', '#HolisticCoach', '#HolisticCoaching', '#HolisticCounselling'
        ];
        
        $coachingIndustryBusiness = [
            '#BusinessProductivity', '#BizCoach', '#BizCoaching', '#BusinessMentorship', '#BusinessStrategyConsultant',
            '#BusinessStrategyCoach', '#BusinessStrategyForWomen', '#BusinessTips101', '#EntrepreneurCoaching', '#EntrepreneurInspiration',
            '#EntrepreneurSkills', '#EntrepreneurAdvice', '#StartupStory', '#SmallBusinessCoaching', '#SmallBusinessConsultant',
            '#SmallBusinessBranding', '#SmallBusinessMarketingTips', '#SmallBusinessStrategy', '#SmallBusinessDevelopment', '#SmallBusinessSuccess'
        ];
        
        $coachingIndustryProfessionalDevelopment = [
            '#CareerCoachingOnline', '#CareerCoachingForWomen', '#CareerClarity', '#CareerCoachingTips', '#CareerAdviceForWomen',
            '#CareerStrategy', '#CareerMotivation', '#CareerGrowthTips', '#CareerChangeCoach', '#CareerPlanning',
            '#JobHuntingTips', '#ProfessionalSuccess', '#CareerAdvancement', '#CareerGrowthGoals', '#NewCareerPath'
        ];
        
        $coachingIndustryPersonalDevelopment = [
            '#SelfImprovementDaily', '#PersonalDevelopmentJourney', '#ContinuousGrowth', '#ProductivityCoach', '#ProductivityHabits', '#IncreaseProductivity'
        ];
        
        // Combined industry tags for English
        $coachingIndustry = array_merge(
            $coachingIndustryGeneral,
            $coachingIndustryBusiness,
            $coachingIndustryProfessionalDevelopment,
            $coachingIndustryPersonalDevelopment
        );

        $coachingNiche = [
            '#PurposeDrivenCoaching', '#ADHDLeadershipCoach', '#NeurodiverseCoaching', '#TheResilienceMethod',
            '#QuietLeadershipCoach', '#CreativeBusinessSupport', '#ValuesLedBusiness'
        ];
        
        $coachingNicheDE = [
            '#StillLeadership', '#WechseljahreCoaching', '#ResilienzMethode', '#PurposeDrivenCoaching',
            '#ADHDLeadershipCoach', '#NeurodiverseCoaching', '#QuietLeadershipCoach', '#CreativeBusinessSupport', '#ValuesLedBusiness'
        ];
        
        $coachingBranded = [
            ['tag' => '#TheConfidenceReset', 'description' => 'coaching programme name'],
            ['tag' => '#CoachingWithSophie', 'description' => 'brand-specific'],
            ['tag' => '#LeadWithPurpose', 'description' => 'vision-driven series or workshop'],
            ['tag' => '#AskCoachDan', 'description' => 'Q&A or video AMA series'],
            ['tag' => '#ThriveByDesign', 'description' => 'method or signature approach'],
            ['tag' => '#GrowWithGraceChallenge', 'description' => 'challenge campaign tag'],
            ['tag' => '#ClientWinsWednesday', 'description' => 'series or proof tag'],
        ];
        
        $coachingBrandedDE = [
            ['tag' => '#DasConfidenceReset', 'description' => 'Coaching-Programmname'],
            ['tag' => '#CoachingMitSophie', 'description' => 'markenspezifisch'],
            ['tag' => '#FühreZielgerichtet', 'description' => 'visionsorientierte Serie oder Workshop'],
            ['tag' => '#FragCoachDan', 'description' => 'Q&A- oder Video-AMA-Serie'],
            ['tag' => '#ErfolgsplanByDesign', 'description' => 'Methode oder Signatur-Ansatz'],
            ['tag' => '#WachseMitJakobChallenge', 'description' => 'Challenge-Kampagnen-Tag'],
            ['tag' => '#KundenerfolgeWednesday', 'description' => 'Serie oder Social-Proof-Tag'],
        ];
        
        $coachingIndustryGeneralDE = [
            '#ZieleErreichen', '#SelbstbewusstseinStärken', '#Erfolgsgewohnheiten', '#Erfolgscoaching', '#CoachingTipps', '#GanzheitlichesCoaching',
            '#GrowthMindsetCoach', '#SelfImprovementJourney', '#CoachLifestyle', '#GoalSettingTips', '#GoalSettingForSuccess',
            '#TransformationCoaching', '#AccountabilityPartner', '#ScaleYourBusiness', '#BreakthroughCoach', '#ConfidenceBuilding',
            '#HighPerformanceHabits', '#MarketingCoaching', '#SuccessCoaching', '#LifestyleCoaching', '#CoachingForLife',
            '#LifeCoachTips', '#CoachingTools', '#HolisticLifeCoach', '#HolisticLifeCoaching', '#HolisticCoach', '#HolisticCoaching', '#HolisticCounselling'
        ];
        
        $coachingIndustryBusinessDE = [
            '#BusinessStrategie', '#UnternehmerTipps', '#SelbstständigkeitAufbauen', '#BusinessProductivity', '#BizCoach', '#BizCoaching',
            '#BusinessMentorship', '#BusinessStrategyConsultant', '#BusinessStrategyCoach', '#BusinessStrategyForWomen', '#BusinessTips101',
            '#EntrepreneurCoaching', '#EntrepreneurInspiration', '#EntrepreneurSkills', '#EntrepreneurAdvice', '#StartupStory',
            '#SmallBusinessCoaching', '#SmallBusinessConsultant', '#SmallBusinessBranding', '#SmallBusinessMarketingTips',
            '#SmallBusinessStrategy', '#SmallBusinessDevelopment', '#SmallBusinessSuccess'
        ];
        
        $coachingIndustryProfessionalDevelopmentDE = [
            '#Karrierecoaching', '#KarriereTipps', '#KarriereWechsel', '#BeruflicheNeuorientierung', '#CareerCoachingOnline',
            '#CareerCoachingForWomen', '#CareerClarity', '#CareerCoachingTips', '#CareerAdviceForWomen', '#CareerStrategy',
            '#CareerMotivation', '#CareerGrowthTips', '#CareerChangeCoach', '#CareerPlanning', '#JobHuntingTips',
            '#ProfessionalSuccess', '#CareerAdvancement', '#CareerGrowthGoals', '#NewCareerPath'
        ];
        
        $coachingIndustryPersonalDevelopmentDE = [
            '#Selbstverbesserung', '#SelfImprovementDaily', '#PersonalDevelopmentJourney', '#ContinuousGrowth',
            '#ProductivityCoach', '#ProductivityHabits', '#IncreaseProductivity'
        ];

        $datasets = [
            [
                'industry' => 'beauty',
                'country' => 'ie',
                'title' => 'Beauty Salon - IRL',
                'intro_title' => 'Hashtag Cheat-Sheet for Beauty Salons in Ireland',
                'local' => ['#BeautySalonIreland', '#SalonIreland', '#IrelandHairdresser', '#IrelandHair', '#NailSalonIreland', '#BrowBarIreland', '#HairAndBeautyIreland', '#DublinSalon', '#CorkHair', '#GalwayNails']
            ],
            [
                'industry' => 'beauty',
                'country' => 'uk',
                'title' => 'Beauty Salon - UK',
                'intro_title' => 'Hashtag Cheat-Sheet for Beauty Salons in the UK',
                'local' => ['#BeautySalonUK', '#SalonUK', '#UKHairdresser', '#UKHair', '#NailSalonUK', '#BrowBarUK', '#HairAndBeautyUK', '#LondonSalon', '#ManchesterHair', '#BirminghamNails', '#GlasgowBrows', '#LiverpoolHair', '#BristolBeauty', '#LeedsHair', '#EdinburghSalon', '#CardiffNails']
            ],
            [
                'industry' => 'beauty',
                'country' => 'de',
                'language' => 'de',
                'title' => 'Beauty Salon - DE',
                'intro_title' => 'Hashtag-Spickzettel für Kosmetikstudios in Deutschland',
                'local' => ['#BeautySalonGermany', '#FriseurDeutschland', '#NagelstudioDeutschland', '#BerlinSalon', '#HamburgFriseur', '#FrankfurtNails', '#StuttgartNägel', '#KosmetikStudioBerlin']
            ],

            [
                'industry' => 'physio',
                'country' => 'ie',
                'title' => 'Physiotherapy Clinic - IRL',
                'intro_title' => 'Hashtag Cheat-Sheet for Physiotherapists in Ireland',
                'local' => ['#IrelandPhysio', '#IrelandPhysiotherapist', '#PhysioClinicIreland', '#PrivatePhysioIreland', '#RehabIreland', '#DublinPhysio', '#CorkPhysio', '#GalwayPhysio']
            ],
            [
                'industry' => 'physio',
                'country' => 'uk',
                'title' => 'Physiotherapy Clinic - UK',
                'intro_title' => 'Hashtag Cheat-Sheet for Physiotherapists in the UK',
                'local' => ['#UKPhysio', '#UKPhysiotherapist', '#PhysioClinicUK', '#PrivatePhysioUK', '#RehabUK', '#LondonPhysio', '#ManchesterPhysio', '#BirminghamPhysio', '#GlasgowPhysio', '#LiverpoolPhysio', '#BristolPhysio', '#LeedsPhysio', '#EdinburghPhysio', '#CardiffPhysio', '#SheffieldPhysio']
            ],
            [
                'industry' => 'physio',
                'country' => 'de',
                'language' => 'de',
                'title' => 'Physiotherapie Praxis - DE',
                'intro_title' => 'Hashtag-Spickzettel für Physiotherapeut:innen in Deutschland',
                'local' => ['#BerlinKlinik', '#FrankfurtPraxis', '#DüsseldorfPhysio']
            ],

            [
                'industry' => 'coaching',
                'country' => 'ie',
                'title' => 'Business Coaching - IRL',
                'intro_title' => 'Hashtag Cheat-Sheet for Coaches in Ireland',
                'local' => ['#IrelandCoach', '#IrelandCoaching', '#IrelandLifeCoach', '#IrelandBusinessCoach', '#IrelandConsultant', '#CoachIreland', '#IrelandEntrepreneurCoaching', '#DublinCoach', '#CorkCoach']
            ],
            [
                'industry' => 'coaching',
                'country' => 'uk',
                'title' => 'Business Coaching - UK',
                'intro_title' => 'Hashtag Cheat-Sheet for Coaches in the UK',
                'local' => ['#UKCoach', '#UKCoaching', '#UKLifeCoach', '#UKBusinessCoach', '#UKConsultant', '#CoachUK', '#UKEntrepreneurCoach', '#LondonCoach', '#ManchesterCoach', '#BirminghamCoach', '#GlasgowCoach', '#LeedsCoach', '#BristolCoach', '#EdinburghCoach', '#LiverpoolCoach', '#CardiffCoach']
            ],
            [
                'industry' => 'coaching',
                'country' => 'de',
                'language' => 'de',
                'title' => 'Business Coaching - DE',
                'intro_title' => 'Hashtag-Spickzettel für Coaches in Deutschland',
                'local' => ['#CoachDeutschland', '#LifeCoachDE', '#BusinessCoachBerlin', '#CoachHamburg', '#CoachKöln', '#CoachStuttgart', '#CoachFrankfurt']
            ],
        ];

        foreach ($datasets as $data) {
            switch ($data['industry']) {
                case 'beauty':
                    if (($data['language'] ?? 'en') === 'de') {
                        $broad = $beautyBroadDE;
                        $industry = array_merge(
                            $beautyIndustryGeneralDE,
                            $beautyIndustryHairSalonsDE,
                            $beautyIndustryWeddingDE,
                            $beautyIndustryNailsDE,
                            $beautyIndustryLashesBrowsDE,
                            $beautyIndustrySkincareFacialsDE,
                            $beautyIndustrySpaWellnessDE,
                            $beautyIndustryAdvancedAestheticsDE
                        );
                        $industryCategories = [
                            'Allgemein' => $beautyIndustryGeneralDE,
                            'Friseure' => $beautyIndustryHairSalonsDE,
                            'Hochzeit' => $beautyIndustryWeddingDE,
                            'Nägel' => $beautyIndustryNailsDE,
                            'Wimpern & Augenbrauen' => $beautyIndustryLashesBrowsDE,
                            'Hautpflege & Gesichtsbehandlungen' => $beautyIndustrySkincareFacialsDE,
                            'Spa & Wellness' => $beautyIndustrySpaWellnessDE,
                            'Ästhetische Behandlungen' => $beautyIndustryAdvancedAestheticsDE,
                        ];
                        $niche = $beautyNicheDE;
                        $branded = $beautyBrandedDE;
                    } else {
                    $broad = $beautyBroad;
                        $industry = array_merge(
                            $beautyIndustryGeneral,
                            $beautyIndustryHairSalons,
                            $beautyIndustryWedding,
                            $beautyIndustryNails,
                            $beautyIndustryLashesBrows,
                            $beautyIndustrySkincareFacials,
                            $beautyIndustrySpaWellness,
                            $beautyIndustryAdvancedAesthetics
                        );
                        $industryCategories = [
                            'General' => $beautyIndustryGeneral,
                            'Hair Salons & Stylists' => $beautyIndustryHairSalons,
                            'Wedding' => $beautyIndustryWedding,
                            'Nails' => $beautyIndustryNails,
                            'Lashes & Brows' => $beautyIndustryLashesBrows,
                            'Skincare & Facials' => $beautyIndustrySkincareFacials,
                            'Spa & Wellness' => $beautyIndustrySpaWellness,
                            'Advanced Aesthetics' => $beautyIndustryAdvancedAesthetics,
                        ];
                    $niche = $beautyNiche;
                    $branded = $beautyBranded;
                    }
                    break;
                case 'physio':
                    if (($data['language'] ?? 'en') === 'de') {
                        $broad = $physioBroadDE;
                        $industry = array_merge(
                            $physioIndustrySportsMusculoskeletalDE,
                            $physioIndustryNeuroDE,
                            $physioIndustryPelvicWomensHealthDE,
                            $physioIndustryPaediatricDE,
                            $physioIndustryGeriatricDE,
                            $physioIndustryChiropracticDE,
                            $physioIndustryMassageTherapyDE,
                            $physioIndustryAcupunctureDE,
                            $physioIndustryAnatomyDE
                        );
                        $industryCategories = [
                            'Sport & Muskuloskelettale Rehabilitation' => $physioIndustrySportsMusculoskeletalDE,
                            'Neurologische Physiotherapie' => $physioIndustryNeuroDE,
                            'Beckenboden & Frauengesundheit' => $physioIndustryPelvicWomensHealthDE,
                            'Kinderphysiotherapie' => $physioIndustryPaediatricDE,
                            'Geriatrie & Sturzprävention' => $physioIndustryGeriatricDE,
                            'Chiropraktik' => $physioIndustryChiropracticDE,
                            'Massagetherapie' => $physioIndustryMassageTherapyDE,
                            'Akupunktur & Komplementärmedizin' => $physioIndustryAcupunctureDE,
                            'Anatomie & Berufliche Weiterbildung' => $physioIndustryAnatomyDE,
                        ];
                        $niche = $physioNicheDE;
                        $branded = $physioBrandedDE;
                    } else {
                    $broad = $physioBroad;
                    $industry = $physioIndustry;
                        $industryCategories = [
                            'Sports & Musculoskeletal Rehab' => $physioIndustrySportsMusculoskeletal,
                            'Neuro Physiotherapy' => $physioIndustryNeuro,
                            'Pelvic & Womenʼs Health' => $physioIndustryPelvicWomensHealth,
                            'Paediatric Physiotherapy' => $physioIndustryPaediatric,
                            'Geriatric & Falls Prevention' => $physioIndustryGeriatric,
                            'Chiropractic' => $physioIndustryChiropractic,
                            'Massage Therapy' => $physioIndustryMassageTherapy,
                            'Acupuncture & Complementary Medicine' => $physioIndustryAcupuncture,
                            'Anatomy & Professional Development' => $physioIndustryAnatomy,
                        ];
                    $niche = $physioNiche;
                    $branded = $physioBranded;
                    }
                    break;
                case 'coaching':
                    if (($data['language'] ?? 'en') === 'de') {
                        $broad = $coachingBroadDE;
                        $industry = array_merge(
                            $coachingIndustryGeneralDE,
                            $coachingIndustryBusinessDE,
                            $coachingIndustryProfessionalDevelopmentDE,
                            $coachingIndustryPersonalDevelopmentDE
                        );
                        $industryCategories = [
                            'Allgemein' => $coachingIndustryGeneralDE,
                            'Business' => $coachingIndustryBusinessDE,
                            'Karriere & berufliche Entwicklung' => $coachingIndustryProfessionalDevelopmentDE,
                            'Persönliche Entwicklung' => $coachingIndustryPersonalDevelopmentDE,
                        ];
                        $niche = $coachingNicheDE;
                        $branded = $coachingBrandedDE;
                    } else {
                    $broad = $coachingBroad;
                    $industry = $coachingIndustry;
                        $industryCategories = [
                            'General' => $coachingIndustryGeneral,
                            'Business' => $coachingIndustryBusiness,
                            'Professional Development' => $coachingIndustryProfessionalDevelopment,
                            'Personal Development' => $coachingIndustryPersonalDevelopment,
                        ];
                    $niche = $coachingNiche;
                    $branded = $coachingBranded;
                    }
                    break;
                default:
                    $broad = $industry = $niche = $branded = [];
                    $industryCategories = null;
            }

            $blockDescriptions = $data['block_descriptions'] ?? [];
            $language = $data['language'] ?? 'en';
            
            $blockTitles = $language === 'de' ? [
                'local' => '1 – Lokal',
                'broad' => '2 – Breit',
                'industry' => '3 – Branche & Expertise',
                'niche' => '4 – Nische',
                'branded' => '5 – Marke',
            ] : [
                'local' => '1 – Local',
                'broad' => '2 – Broad',
                'industry' => '3 – Industry & Expertise',
                'niche' => '4 – Niche',
                'branded' => '5 – Branded',
            ];

            $blocks = [
                [
                    'title' => $blockTitles['local'],
                    'description' => $blockDescriptions['local'] ?? null,
                    'tags' => $data['local'],
                ],
                [
                    'title' => $blockTitles['broad'],
                    'description' => $blockDescriptions['broad'] ?? null,
                    'tags' => $broad,
                ],
                [
                    'title' => $blockTitles['industry'],
                    'description' => $blockDescriptions['industry'] ?? null,
                    'tags' => $industry,
                    'categories' => $industryCategories ?? null,
                ],
                [
                    'title' => $blockTitles['niche'],
                    'description' => $blockDescriptions['niche'] ?? null,
                    'tags' => $niche,
                ],
                [
                    'title' => $blockTitles['branded'],
                    'description' => $blockDescriptions['branded'] ?? null,
                    'tags' => $branded,
                ],
            ];

            $brandedTags = array_map(function($item) {
                return is_array($item) && isset($item['tag']) ? $item['tag'] : $item;
            }, $branded);
            
            $allTags = array_values(array_unique(array_merge(
                $data['local'],
                $broad,
                $industry,
                $niche,
                $brandedTags
            )));

            $guidelines = $data['guidelines'] ?? [];

            Hashtag::updateOrCreate(
                [
                    'industry' => $data['industry'],
                    'country' => $data['country'],
                    'language' => $data['language'] ?? 'en',
                ],
                [
                    'title' => $data['title'],
                    'tags' => $allTags,
                    'intro_title' => $data['intro_title'] ?? null,
                    'intro_description' => $data['intro_description'] ?? null,
                    'guidelines' => $guidelines,
                    'hashtag_blocks' => $blocks,
                ]
            );
        }
    }
}
